<?php

  // The database and storage tools of a packed application's runtime (wasm/app/pad-app.js),
  // run in its own PHP for the panel and the /_pad/ calls - never by an application page:
  //
  //   databases   every SQLite file under DATA/, its tables and their rows
  //   rows        one table, a page of it
  //   query       statements typed in the panel's console, with the rows of the last
  //   export      a database as the .sqlite file or as SQL (a dump sqlite3 would read back)
  //   import      a .sqlite file or SQL that becomes the database - checked before it does
  //   reset       the database gone, built again by the application on its next request
  //   files       the files under DATA/
  //   file        one of them, to download
  //   backup      all of DATA/ as one zip; restore puts such a zip back
  //
  // The call comes in /pad/runtime/in.json, a file sent with it in /pad/runtime/upload.bin.
  // The answer is JSON; a file to download is left in /pad/runtime/ (or named in DATA/) and
  // the runtime takes it from there.

  const RT_DATA = '/pad/DATA/';
  const RT_DIR  = '/pad/runtime/';
  const RT_ROWS = 1000;

  include '/pad/pad/lib/db.php';           // padDbComment, which the statement splitter uses
  include '/pad/pad/lib/migrate.php';      // padMigrateSplit

  date_default_timezone_set ( 'UTC' );

  $rtIn     = json_decode ( (string) @file_get_contents ( RT_DIR . 'in.json' ), TRUE ) ?: [];
  $rtAction = (string) ( $rtIn ['action'] ?? '' );

  try {

    $rtAnswer = match ( $rtAction ) {
      'databases' => rtDatabases (),
      'rows'      => rtRows    ( rtDb ( $rtIn ['db'] ?? '' ), (string) ( $rtIn ['table'] ?? '' ), (int) ( $rtIn ['offset'] ?? 0 ), (int) ( $rtIn ['limit'] ?? 50 ) ),
      'query'     => rtQuery   ( rtDb ( $rtIn ['db'] ?? '' ), (string) ( $rtIn ['sql'] ?? '' ) ),
      'export'    => rtExport  ( rtDb ( $rtIn ['db'] ?? '' ), (string) ( $rtIn ['format'] ?? 'sqlite' ) ),
      'import'    => rtImport  ( rtDb ( $rtIn ['db'] ?? '', TRUE ) ),
      'reset'     => rtReset   ( rtDb ( $rtIn ['db'] ?? '' ) ),
      'files'     => rtFiles   (),
      'file'      => rtFile    ( (string) ( $rtIn ['path'] ?? '' ) ),
      'backup'    => rtBackup  ( (string) ( $rtIn ['name'] ?? 'pad' ) ),
      'restore'   => rtRestore (),
      default     => throw new Exception ( "there is no call '$rtAction'" )
    };

  } catch ( Throwable $e ) {

    $rtAnswer = [ 'error' => $e->getMessage () ];

  }

  echo json_encode ( $rtAnswer, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE );

  // A database named by its path under DATA/: a file that is there and is SQLite - or, for
  // an import, a name of one that may not be there yet.

  function rtDb ( $name, $new = FALSE ) {

    $name = trim ( str_replace ( '\\', '/', (string) $name ), '/' );

    if ( $name === '' or str_contains ( "/$name/", '/../' ) or str_contains ( "/$name/", '/./' ) or str_contains ( $name, "\0" ) )
      throw new Exception ( 'name a database by its path under DATA/' );

    $file = RT_DATA . $name;

    if ( is_file ( $file ) ) {
      if ( ! rtIsSqlite ( $file ) )
        throw new Exception ( "DATA/$name is no SQLite database" );
    } elseif ( ! $new )
      throw new Exception ( "there is no database DATA/$name" );
    elseif ( ! preg_match ( '/\.(sqlite3?|db)$/i', $name ) )
      throw new Exception ( 'a new database ends in .sqlite, .sqlite3 or .db' );

    return [ 'name' => $name, 'file' => $file ];

  }

  function rtIsSqlite ( $file ) {

    $handle = @fopen ( $file, 'rb' );

    if ( ! $handle )
      return FALSE;

    $head = fread ( $handle, 16 );
    fclose ( $handle );

    return $head === "SQLite format 3\0";

  }

  function rtOpen ( $file ) {

    $pdo = new PDO ( "sqlite:$file" );
    $pdo->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $pdo->setAttribute ( PDO::ATTR_STRINGIFY_FETCHES, FALSE );

    return $pdo;

  }

  function rtQuote ( $name ) {

    return '"' . str_replace ( '"', '""', $name ) . '"';

  }

  function rtWalk ( $dir = RT_DATA ) {

    $found = [];

    if ( ! is_dir ( $dir ) )
      return $found;

    $iterator = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $dir, FilesystemIterator::SKIP_DOTS ) );

    foreach ( $iterator as $one )
      if ( $one->isFile () )
        $found [] = substr ( str_replace ( '\\', '/', $one->getPathname () ), strlen ( RT_DATA ) );

    sort ( $found );

    return $found;

  }

  function rtDatabases () {

    $list = [];

    foreach ( rtWalk () as $name ) {

      $file = RT_DATA . $name;

      if ( ! rtIsSqlite ( $file ) )
        continue;

      $pdo    = rtOpen ( $file );
      $tables = [];

      foreach ( $pdo->query ( "SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' ORDER BY type, name" ) as $row )
        $tables [] = [
          'name' => $row ['name'],
          'type' => $row ['type'],
          'rows' => (int) $pdo->query ( 'SELECT count(*) FROM ' . rtQuote ( $row ['name'] ) )->fetchColumn ()
        ];

      $list [] = [ 'name' => $name, 'size' => filesize ( $file ), 'modified' => filemtime ( $file ), 'tables' => $tables ];

    }

    return [ 'databases' => $list ];

  }

  function rtRows ( $db, $table, $offset, $limit ) {

    $pdo   = rtOpen ( $db ['file'] );
    $check = $pdo->prepare ( "SELECT count(*) FROM sqlite_master WHERE type IN ('table', 'view') AND name = ?" );
    $check->execute ( [ $table ] );

    if ( ! $check->fetchColumn () )
      throw new Exception ( "there is no table '$table' in DATA/{$db ['name']}" );

    $limit  = max ( 1, min ( RT_ROWS, $limit ) );
    $offset = max ( 0, $offset );
    $total  = (int) $pdo->query ( 'SELECT count(*) FROM ' . rtQuote ( $table ) )->fetchColumn ();
    $query  = $pdo->query ( 'SELECT * FROM ' . rtQuote ( $table ) . " LIMIT $limit OFFSET $offset" );

    return [ 'table' => $table, 'total' => $total, 'offset' => $offset ] + rtResult ( $query );

  }

  function rtResult ( $statement ) {

    $columns = [];

    for ( $i = 0; $i < $statement->columnCount (); $i++ )
      $columns [] = $statement->getColumnMeta ( $i ) ['name'] ?? "column $i";

    $rows = [];

    while ( count ( $rows ) < RT_ROWS and ( $row = $statement->fetch ( PDO::FETCH_NUM ) ) !== FALSE )
      $rows [] = array_map ( fn ( $value ) => is_string ( $value ) && ! mb_check_encoding ( $value, 'UTF-8' ) ? '(' . strlen ( $value ) . ' bytes)' : $value, $row );

    return [ 'columns' => $columns, 'rows' => $rows, 'more' => $statement->fetch () !== FALSE ];

  }

  // Every statement runs in turn; the answer of each is its rows, or the rows it changed.
  // The first that fails ends it, named with its place - what ran before it stays done.

  function rtQuery ( $db, $sql ) {

    $pdo     = rtOpen ( $db ['file'] );
    $results = [];

    foreach ( padMigrateSplit ( $sql, FALSE ) as $n => $statement ) {

      $start = microtime ( TRUE );

      try {
        $query = $pdo->query ( $statement );
      } catch ( Throwable $e ) {
        return [ 'results' => $results, 'error' => 'statement ' . ( $n + 1 ) . ': ' . $e->getMessage () ];
      }

      $result = $query->columnCount () ? rtResult ( $query ) : [ 'changes' => $query->rowCount () ];

      $results [] = [ 'sql' => $statement, 'ms' => round ( ( microtime ( TRUE ) - $start ) * 1000, 1 ) ] + $result;

    }

    return [ 'results' => $results ];

  }

  function rtExport ( $db, $format ) {

    $base = preg_replace ( '/\.[^.\/]*$/', '', basename ( $db ['name'] ) );

    if ( $format == 'sqlite' )
      return [ 'file' => $db ['file'], 'name' => basename ( $db ['name'] ), 'type' => 'application/vnd.sqlite3' ];

    if ( $format != 'sql' )
      throw new Exception ( "export as sqlite or sql, not as '$format'" );

    file_put_contents ( RT_DIR . 'export.sql', rtDump ( rtOpen ( $db ['file'] ), $db ['name'] ) );

    return [ 'file' => RT_DIR . 'export.sql', 'name' => "$base.sql", 'type' => 'application/sql; charset=utf-8', 'remove' => TRUE ];

  }

  // The database as the SQL that builds it again - the shape of sqlite3's .dump: the
  // tables with their rows, then the indexes, triggers and views. SQLite writes every value
  // itself, with quote(): text quoted, a blob as X'..', a number as it is, NULL.

  function rtDump ( $pdo, $name ) {

    $out  = "-- DATA/$name, exported by PAD in the browser on " . gmdate ( 'Y-m-d H:i:s' ) . " UTC\n";
    $out .= "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\n";

    $tables = $pdo->query ( "SELECT name, sql FROM sqlite_master WHERE type = 'table' AND sql IS NOT NULL ORDER BY rowid" )->fetchAll ( PDO::FETCH_ASSOC );

    foreach ( $tables as $table ) {

      if ( $table ['name'] == 'sqlite_sequence' )
        continue;

      $out .= ( str_starts_with ( $table ['name'], 'sqlite_' ) ? '' : $table ['sql'] . ";\n" );

      $columns = array_map ( fn ( $column ) => 'quote(' . rtQuote ( $column ['name'] ) . ')',
                             $pdo->query ( 'PRAGMA table_info(' . rtQuote ( $table ['name'] ) . ')' )->fetchAll ( PDO::FETCH_ASSOC ) );

      if ( ! $columns )
        continue;

      $rows = $pdo->query ( 'SELECT ' . implode ( " || ',' || ", $columns ) . ' FROM ' . rtQuote ( $table ['name'] ) );

      while ( ( $values = $rows->fetchColumn () ) !== FALSE )
        $out .= 'INSERT INTO ' . rtQuote ( $table ['name'] ) . " VALUES($values);\n";

    }

    foreach ( $tables as $table )
      if ( $table ['name'] == 'sqlite_sequence' ) {
        $out .= "DELETE FROM sqlite_sequence;\n";
        foreach ( $pdo->query ( "SELECT quote(name) || ',' || quote(seq) FROM sqlite_sequence" )->fetchAll ( PDO::FETCH_COLUMN ) as $values )
          $out .= "INSERT INTO sqlite_sequence VALUES($values);\n";
      }

    foreach ( $pdo->query ( "SELECT sql FROM sqlite_master WHERE type IN ('index', 'trigger', 'view') AND sql IS NOT NULL ORDER BY CASE type WHEN 'index' THEN 1 WHEN 'trigger' THEN 2 ELSE 3 END, rowid" )->fetchAll ( PDO::FETCH_COLUMN ) as $sql )
      $out .= "$sql;\n";

    return "{$out}COMMIT;\n";

  }

  // What comes in becomes the database only once it is one: a .sqlite file has to pass
  // SQLite's integrity check, SQL has to build a database without an error. The new file
  // then takes the old one's place in one rename, its journal files gone with it.

  function rtImport ( $db ) {

    $upload = RT_DIR . 'upload.bin';
    $work   = $db ['file'] . '.import';

    if ( ! is_file ( $upload ) or ! filesize ( $upload ) )
      throw new Exception ( 'nothing to import - send a .sqlite file or SQL' );

    @mkdir ( dirname ( $db ['file'] ), 0777, TRUE );
    @unlink ( $work );

    if ( rtIsSqlite ( $upload ) )
      copy ( $upload, $work );

    else {

      $sql = file_get_contents ( $upload );

      if ( ! mb_check_encoding ( $sql, 'UTF-8' ) )
        throw new Exception ( 'this is no SQLite file and no SQL text' );

      try {
        rtOpen ( $work )->exec ( $sql );
      } catch ( Throwable $e ) {
        @unlink ( $work );
        throw new Exception ( 'the SQL did not build a database: ' . $e->getMessage () );
      }

    }

    try {
      $check = rtOpen ( $work )->query ( 'PRAGMA integrity_check' )->fetchColumn ();
    } catch ( Throwable $e ) {
      $check = $e->getMessage ();
    }

    if ( $check !== 'ok' ) {
      @unlink ( $work );
      throw new Exception ( "the file is no sound database: $check" );
    }

    foreach ( [ '-journal', '-wal', '-shm' ] as $tail )
      @unlink ( $db ['file'] . $tail );

    rename ( $work, $db ['file'] );

    return [ 'imported' => $db ['name'], 'size' => filesize ( $db ['file'] ) ] + rtDatabaseTables ( $db ['file'] );

  }

  function rtDatabaseTables ( $file ) {

    $pdo = rtOpen ( $file );

    return [ 'tables' => $pdo->query ( "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name" )->fetchAll ( PDO::FETCH_COLUMN ) ];

  }

  function rtReset ( $db ) {

    foreach ( [ '', '-journal', '-wal', '-shm', '.lock' ] as $tail )
      @unlink ( $db ['file'] . $tail );

    return [ 'reset' => $db ['name'] ];

  }

  function rtFiles () {

    $files = [];

    foreach ( rtWalk () as $name )
      $files [] = [ 'path' => $name, 'size' => filesize ( RT_DATA . $name ), 'modified' => filemtime ( RT_DATA . $name ), 'sqlite' => rtIsSqlite ( RT_DATA . $name ) ];

    return [ 'files' => $files ];

  }

  function rtFile ( $path ) {

    $path = trim ( str_replace ( '\\', '/', $path ), '/' );

    if ( $path === '' or str_contains ( "/$path/", '/../' ) or ! is_file ( RT_DATA . $path ) )
      throw new Exception ( "there is no file DATA/$path" );

    return [ 'file' => RT_DATA . $path, 'name' => basename ( $path ), 'type' => 'application/octet-stream' ];

  }

  function rtBackup ( $name ) {

    $zip  = new ZipArchive;
    $file = RT_DIR . 'backup.zip';

    @unlink ( $file );

    if ( $zip->open ( $file, ZipArchive::CREATE ) !== TRUE )
      throw new Exception ( 'the backup could not be written' );

    $zip->addFromString ( 'pad-backup.json', json_encode ( [ 'pack' => $name, 'made' => gmdate ( 'c' ) ] ) );

    foreach ( rtWalk () as $one )
      $zip->addFile ( RT_DATA . $one, "DATA/$one" );

    $zip->close ();

    $safe = preg_replace ( '/[^A-Za-z0-9_-]+/', '-', $name );

    return [ 'file' => $file, 'name' => "$safe-data-" . gmdate ( 'Y-m-d' ) . '.zip', 'type' => 'application/zip', 'remove' => TRUE ];

  }

  // A backup puts DATA/ back as it was: every entry under DATA/ in the zip, nothing outside
  // it, no name that climbs out - and only once the whole zip has been read.

  function rtRestore () {

    $zip = new ZipArchive;

    if ( $zip->open ( RT_DIR . 'upload.bin' ) !== TRUE )
      throw new Exception ( 'this is no zip file' );

    $entries = [];

    for ( $i = 0; $i < $zip->numFiles; $i++ ) {

      $name = $zip->getNameIndex ( $i );

      if ( str_ends_with ( $name, '/' ) or $name == 'pad-backup.json' )
        continue;

      if ( ! str_starts_with ( $name, 'DATA/' ) or str_contains ( "/$name/", '/../' ) or str_contains ( $name, '\\' ) or str_contains ( $name, "\0" ) )
        throw new Exception ( "the zip holds '$name', which is no file of DATA/ - no backup of PAD in the browser" );

      $entries [substr ( $name, 5 )] = $zip->getFromIndex ( $i );

    }

    if ( ! $entries )
      throw new Exception ( 'the zip holds no file of DATA/' );

    foreach ( array_reverse ( rtWalk () ) as $one )
      @unlink ( RT_DATA . $one );

    foreach ( $entries as $name => $content ) {
      @mkdir ( dirname ( RT_DATA . $name ), 0777, TRUE );
      file_put_contents ( RT_DATA . $name, $content );
    }

    return [ 'restored' => count ( $entries ) ];

  }

?>
