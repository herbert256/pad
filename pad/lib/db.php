<?php

  // The whole database layer. db() runs against the application database configured in
  // _config/config.php; padDb() against PAD's own database ($padSqlPad* - sessions,
  // links, logs). Both connect lazily and cache the connection in a global.
  //
  // The application database speaks one of two drivers, $padSqlDriver:
  //   'mysql'   mysqli on $padSqlHost/User/Password/Database - db() answers FALSE outright
  //             when mysqli is not installed, which is how PAD runs without a database
  //   'sqlite'  PDO on the file $padSqlDatabase names - a relative name lives under DATA/ -
  //             and $padSqlSetup, when given, is a .sql file that builds the database the
  //             first time, when the file does not exist yet: an application with no
  //             database server at all
  // PAD's own database is MySQL. The verbs, the placeholders and the shapes below are the
  // same whatever the driver: padDbRun is the one place that talks to either.
  //
  // padDbConnect connects and puts MySQL in TRADITIONAL sql_mode, so bad data errors
  // instead of being silently truncated.
  //
  // padDbPart2 does the work and gives PAD's SQL its shape:
  //   - {0}, {1} ... placeholders are replaced from $vars by padDbPlaceholders: escaped
  //     inside a quoted literal, a number or a quoted literal when written bare, raw when
  //     the key starts with x; {0:20} truncates to that length first
  //   - the leading verb decides the shape of the result: CHECK (rowcount, note the
  //     "CHECK table WHERE ..." form), FIELD (one scalar), RECORD (one assoc row), ARRAY
  //     or SELECT (rows, keyed by an id column when the query returns one), INSERT (the
  //     new id, else the row count), UPDATE/DELETE/REPLACE/SET/TRUNCATE/LOAD (row count)
  //   - every statement is appended to $_SQL; failures go through padError, and
  //     events/sql.php logs the query when info is on; a statement of db() that ran is
  //     also told to the application's _events/sql.php (lib/events.php)

  function db ( $sql, $vars = [] ) {

    // A replayed request never writes: a statement that would change the database is not
    // sent, and answers as if it changed nothing (lib/replay.php).

    if ( padReplaying () and padReplayWrites ( $sql ) )
      return 0;

    $connect = padDbApp ();

    if ( $connect === NULL )
      return FALSE;

    return padDbPart2 ( $connect, $sql, $vars, TRUE );

  }

  // The application's connection, made on first use: a mysqli link or a PDO handle, FALSE
  // when connecting failed - the connect reported why - and NULL when the driver is MySQL
  // and mysqli is not installed.

  function padDbApp () {

    global $padSqlConnect, $padSqlDriver, $padSqlHost, $padSqlUser, $padSqlPassword,
           $padSqlDatabase, $padSqlSetup;

    if ( isset ( $padSqlConnect ) )
      return $padSqlConnect;

    $driver = $padSqlDriver ?? 'mysql';

    if ( $driver == 'sqlite' )
      return $padSqlConnect = padDbSqlite ( $padSqlDatabase, $padSqlSetup ?? '' );

    if ( $driver != 'mysql' )
      return $padSqlConnect = padError ( "there is no database driver named '" . padMakeSafe ( $driver, 20 ) . "' - 'mysql' or 'sqlite'" );

    if ( ! function_exists ( 'mysqli_connect' ) )
      return NULL;

    return $padSqlConnect = padDbConnect ( $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase );

  }

  function padDb ( $sql, $vars = [] ) {

    if ( ! function_exists ( 'mysqli_connect' ) )
      return FALSE;

    global $padSqlPadConnect, $padSqlPadHost , $padSqlPadUser , $padSqlPadPassword , $padSqlPadDatabase;

    if ( ! isset ( $padSqlPadConnect ) )
      $padSqlPadConnect = padDbConnect ( $padSqlPadHost , $padSqlPadUser , $padSqlPadPassword , $padSqlPadDatabase );

    return padDbPart2 ($padSqlPadConnect, $sql, $vars);

  }

  // mysqli reports by return value here, as this file is written for: since PHP 8.1 it
  // throws by default, so the failure branches below never ran - the message the try guard
  // gave lacked the statement, and under the continuing actions the exception abandoned
  // the calling PHP file instead of db() answering.

  function padDbConnect ( $host, $user, $password, $database ) {

    mysqli_report ( MYSQLI_REPORT_OFF );

    $connect = @mysqli_connect ( "$host" , $user , $password , $database );

    if ( ! $connect )
      return padError ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );

    mysqli_query($connect, "SET SESSION sql_mode = 'TRADITIONAL'");

    // Integer and floating point columns come back as PHP numbers, everything else - text,
    // dates, the exact DECIMAL - as the string it is. Every column came back a string, and
    // {reactData} and {select htmlAttrJson} guessed the numbers back from the values, which
    // made a username "2024" an int and an id "007" a 7.

    mysqli_options ( $connect, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE );

    return $connect;

  }

  // The SQLite connection, through PDO, reporting by return value as the mysqli one does.
  // The file is created by the first connection when it does not exist; with a setup file -
  // a relative name is in the application's directory - it is built from that first - under a lock, into a file of its own that is renamed into
  // place, so a second request arriving meanwhile never finds a half-built database.
  // Integers and floats come back as PHP numbers, as MySQL's do here.

  function padDbSqlite ( $file, $setup = '' ) {

    global $padDirMode;

    if ( ! class_exists ( 'PDO' ) or ! in_array ( 'sqlite', PDO::getAvailableDrivers () ) )
      return padError ( 'SQLite: the pdo_sqlite extension is not installed' );

    $file = (string) $file;

    if ( $file == '' )
      return padError ( 'SQLite: $padSqlDatabase names no database file' );

    if ( $file != ':memory:' and ! str_starts_with ( $file, '/' ) and ! preg_match ( '#^[A-Za-z]:[\\\\/]#', $file ) )
      $file = DATA . $file;

    if ( $setup and ! str_starts_with ( $setup, '/' ) and ! preg_match ( '#^[A-Za-z]:[\\\\/]#', $setup ) )
      $setup = APP . $setup;

    if ( $file != ':memory:' and ! is_dir ( dirname ( $file ) ) )
      @mkdir ( dirname ( $file ), $padDirMode ?? 0755, TRUE );

    if ( $setup and $file != ':memory:' and ! file_exists ( $file ) )
      if ( ! padDbSqliteSetup ( $file, $setup ) )
        return FALSE;

    try {
      $connect = new PDO ( "sqlite:$file" );
    } catch ( Throwable $e ) {
      return padError ( 'SQLite: ' . $e->getMessage () . " / $file" );
    }

    $connect->setAttribute ( PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT );
    $connect->setAttribute ( PDO::ATTR_TIMEOUT,           10 );
    $connect->setAttribute ( PDO::ATTR_STRINGIFY_FETCHES, FALSE );

    if ( $setup and $file == ':memory:' )
      if ( $connect->exec ( (string) padFileGet ( $setup ) ) === FALSE )
        return padError ( 'SQLite setup ' . basename ( $setup ) . ': ' . $connect->errorInfo () [2] );

    $connect->exec ( 'PRAGMA foreign_keys = ON' );

    return $connect;

  }

  function padDbSqliteSetup ( $file, $setup ) {

    if ( ! is_file ( $setup ) )
      return padError ( "SQLite: the setup file $setup does not exist" );

    $lock = fopen ( "$file.lock", 'c' );

    if ( $lock )
      flock ( $lock, LOCK_EX );

    try {

      if ( file_exists ( $file ) )
        return TRUE;

      $work = "$file." . getmypid () . '.new';

      @unlink ( $work );

      $build = new PDO ( "sqlite:$work" );
      $build->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );

      if ( $build->exec ( (string) file_get_contents ( $setup ) ) === FALSE ) {
        $error = $build->errorInfo () [2];
        $build = NULL;
        @unlink ( $work );
        return padError ( 'SQLite setup ' . basename ( $setup ) . ": $error" );
      }

      $build = NULL;

      return rename ( $work, $file );

    } catch ( Throwable $e ) {

      return padError ( 'SQLite setup ' . basename ( $setup ) . ': ' . $e->getMessage () );

    } finally {

      if ( $lock ) {
        flock ( $lock, LOCK_UN );
        fclose ( $lock );
      }

    }

  }

  // $event: the statement is the application's own - db(), not padDb() on PAD's sessions
  // and caches - so the application's _events/sql.php hears it, with how long it took.

  function padDbPart2 ( $padSqlConnect, $sql, $vars, $event = FALSE ) {

    global $_SQL, $pad, $padDataSetRecord, $padInfo, $padPrm;

    $input = $sql;

    if ( count ( $vars ) and $padSqlConnect )
      $sql = padDbPlaceholders ( $padSqlConnect, $sql, $vars );

    // The verb ends at any white space, as SQL has it: split on a space alone, a statement
    // with a newline or a tab after its verb - a named query's select on a line of its own -
    // was no verb db() knew, so its rows were answered as '' and a record, field or check
    // went to the database as written and failed there.

    $split   = preg_split ( '/\s+/', trim ( $sql ), 2 );
    $command = strtolower ( $split [0] );

    if ($command == 'select')
      $command = 'array';

    if     ( $command == 'check'  )  $sql = 'select 1 from ' . $split[1] . ' limit 0,1';
    elseif ( $command == 'record' )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'field'  )  $sql = 'select '        . $split[1] . ' limit 0,1';
    elseif ( $command == 'array'  )  $sql = 'select '        . $split[1];

    $_SQL [] = $sql;

    // No connection - the connect reported why - or a failed statement: reported with the
    // statement, and db() answers the empty shape of its command, as for no rows: '' for a
    // field, [] for a record or an array, FALSE for anything else.

    $start = hrtime ( TRUE );
    $run   = $padSqlConnect ? padDbRun ( $padSqlConnect, $sql, $command ) : FALSE;
    $ms    = ( hrtime ( TRUE ) - $start ) / 1e6;

    if ( ! $run ) {

      if ( $padSqlConnect )
        padError ( 'SQL: ' . padDbError ( $padSqlConnect ) . ' / '. $sql );

      if ( $command == 'field' )                       return '';
      if ( $command == 'record' or $command == 'array' ) return [];
      return FALSE;

    }

    $rows   = $run ['rows'];
    $fields = $run ['first'];

    if     ( $command == 'insert'  ) {
      $return = $run ['id'];
      if ( !$return )
        $return = $rows;
    }
    elseif ( $command == 'set')       $return = $rows;
    elseif ( $command == 'truncate')  $return = $rows;
    elseif ( $command == 'load'    )  $return = $rows;
    elseif ( $command == 'replace' )  $return = $rows;
    elseif ( $command == 'update'  )  $return = $rows;
    elseif ( $command == 'delete'  )  $return = $rows;
    elseif ( $command == 'check'   )  $return = $rows;
    elseif ( $command == 'field'   )
      if ( $rows < 1 or ! $fields )
        $return = '';
      else
        foreach ($fields as $key => $return)
          break;
    elseif ( $command == 'record'  )
      if ( $rows < 1 or ! $fields )
        $return = array();
      else {
        $return = $fields;
        $padDataSetRecord [] = $fields;
      }
    elseif ( $command == 'array'  ) {
      $return = array();
      foreach ( $run ['all'] as $record )
        if ( isset($record['id']) and !isset($return [$record['id']]) )
          $return [$record['id']] = $record;
        else
          $return [] = $record;
    }
    else
      $return = '';

    if ( $padInfo )
      include PAD . 'events/sql.php';

    if ( $event === TRUE )
      padEvent ( 'sql', [ 'sql' => $sql, 'input' => $input, 'vars' => $vars, 'result' => $return,
                          'rows' => $rows, 'ms' => $ms ] );

    return $return;

  }

  // Runs one statement on either driver and answers what padDbPart2 shapes the result
  // from - the row count (affected rows for a write, rows returned for a read), the first
  // row for field and record, every row for array, the new id for insert - or FALSE when
  // the statement failed.

  function padDbRun ( $connect, $sql, $command ) {

    $run = [ 'rows' => 0, 'first' => NULL, 'all' => [], 'id' => 0 ];

    if ( $connect instanceof PDO ) {

      $query = $connect->query ( $sql );

      if ( $query === FALSE )
        return FALSE;

      if ( $query->columnCount () ) {
        $run ['all']  = $query->fetchAll ( PDO::FETCH_ASSOC );
        $run ['rows'] = count ( $run ['all'] );
      } else
        $run ['rows'] = $query->rowCount ();

      $run ['first'] = $run ['all'] [0] ?? NULL;

      if ( $command == 'insert' )
        $run ['id'] = (int) $connect->lastInsertId ();

      return $run;

    }

    $query = mysqli_query ( $connect , $sql );

    if ( ! $query )
      return FALSE;

    $run ['rows'] = mysqli_affected_rows ( $connect );

    if ( $run ['rows'] > 0 and ( $command == 'field' or $command == 'record' ) )
      $run ['first'] = mysqli_fetch_assoc ( $query );

    if ( $run ['rows'] > 0 and $command == 'array' )
      while ( $record = mysqli_fetch_assoc ( $query ) )
        $run ['all'] [] = $record;

    if ( $command == 'insert' )
      $run ['id'] = mysqli_insert_id ( $connect );

    return $run;

  }

  function padDbError ( $connect ) {

    if ( $connect instanceof PDO ) {
      $info = $connect->errorInfo ();
      return ( $info [1] ?? $info [0] ?? '' ) . ': ' . ( $info [2] ?? 'unknown error' );
    }

    return mysqli_errno ( $connect ) . ': ' . mysqli_error ( $connect );

  }

  // A named query: the text of a _data/name.sql file, which {name} then iterates. Every
  // {$field} in it is resolved where the tag stands - an option of the tag itself first
  // ({topCustomers country='USA'}), then a field or a variable of the page - and bound as a placeholder,
  // so it reaches the database as an escaped literal, a number, or for an array a list. The file
  // reads: its statement must be a SELECT or one of db()'s reading verbs.

  function padDbNamed ( $sql, $file ) {

    global $padCheckSyntax;

    $vars = [];

    // Line comments are the file's own notes: -- at the start of a line.

    $sql = preg_replace ( '/^[ \t]*--.*$/m', '', $sql );

    $sql = preg_replace_callback ( '/\{\$([A-Za-z_][A-Za-z0-9_]*)\}/',

      function ( $match ) use ( &$vars, $file, $padCheckSyntax ) {

        global $pad, $padPrm;

        $name = $match [1];
        $key  = 'p' . count ( $vars );

        if     ( isset ( $padPrm [$pad] [$name] ) ) $vars [$key] = padTagParm ( $name );
        elseif ( padArrayCheck ( $name ) ) $vars [$key] = padArrayValue ( $name );
        elseif ( padFieldCheck ( $name ) ) $vars [$key] = padFieldValue ( $name );
        else {
          if ( $padCheckSyntax )
            padError ( "the named query " . basename ( $file ) . " reads {\$$name}, and there is no field named '$name'" );
          $vars [$key] = '';
        }

        return '{' . $key . '}';

      }, $sql );

    $verb = strtolower ( strtok ( ltrim ( $sql ), " \t\r\n" ) );

    if ( ! in_array ( $verb, [ 'select', 'array', 'record', 'field', 'check' ] ) )
      return padError ( "the named query " . basename ( $file ) . " must read - it starts with '" . padMakeSafe ( $verb, 20 ) . "'" );

    return db ( trim ( $sql ), $vars );

  }

  // Fills the {0}, {1} ... placeholders in one pass over the statement, so a value that
  // itself holds {1} is never substituted again by the next placeholder. A placeholder the
  // statement writes inside a quoted literal - name = '{0}' - gets the value escaped, as it
  // always did. One written bare - id = {0} - gets a number as a number and anything else
  // as a quoted, escaped literal: escaping alone protected nothing there, and the documented
  // id = {0} took '0 or 1=1' as SQL. An array there becomes a list, for IN ({0}). Keys
  // starting x are inserted raw, a deliberate escape hatch; {0:20} cuts the value to that
  // many characters first.
  //
  // A comment - -- or # to the end of the line, /* to */ - is copied as it stands, its
  // placeholders unfilled: it is no quoted literal, and an apostrophe in it - "-- the
  // staff's count" - opened one, so every placeholder after it counted as quoted and got its
  // value escaped but not quoted, and a bare id = {0} took '0 or 1=1' as SQL again. MySQL
  // wants white space after -- and knows #; SQLite takes -- as it is and has no #.

  function padDbPlaceholders ( $connect, $sql, $vars ) {

    $out   = '';
    $len   = strlen ( $sql );
    $quote = '';
    $slash = ! ( $connect instanceof PDO );

    for ( $i = 0; $i < $len; $i++ ) {

      $char = $sql [$i];

      if ( ! $quote and ( $end = padDbComment ( $sql, $i, $slash ) ) ) {
        $out .= substr ( $sql, $i, $end - $i );
        $i    = $end - 1;
        continue;
      }

      if ( $quote ) {

        if ( $slash and $char == '\\' and $i + 1 < $len ) {
          $out .= $char . $sql [++$i];
          continue;
        }

        if ( $char == $quote )
          $quote = '';

      } elseif ( $char == "'" or $char == '"' or $char == '`' )

        $quote = $char;

      if ( $char == '{' and preg_match ( '/\G\{([A-Za-z0-9_]+)(?::(\d+))?\}/', $sql, $match, 0, $i ) ) {

        $key = $match [1];

        if ( ! array_key_exists ( $key, $vars ) ) {
          $out .= $char;
          continue;
        }

        $value = $vars [$key];

        if ( isset ( $match [2] ) and is_scalar ( $value ) )
          $value = mb_substr ( (string) $value, 0, (int) $match [2] );

        if     ( $key [0] == 'x'    ) $out .= is_array ( $value ) ? implode ( ',', $value ) : $value;
        elseif ( $quote == '`'      ) $out .= str_replace ( '`', '``', (string) $value );
        elseif ( $quote             ) $out .= padDbEscape ( $connect, $value, $quote );
        else                          $out .= padDbLiteral ( $connect, $value );

        $i += strlen ( $match [0] ) - 1;
        continue;

      }

      $out .= $char;

    }

    return $out;

  }

  // Where the comment that starts at $i ends - the offset after it - or 0 when no comment
  // starts there. $mysql: -- needs white space after it, and # is a comment too.

  function padDbComment ( $sql, $i, $mysql ) {

    $two = substr ( $sql, $i, 2 );

    if ( $two == '/*' ) {
      $close = strpos ( $sql, '*/', $i + 2 );
      return ( $close === FALSE ) ? strlen ( $sql ) : $close + 2;
    }

    $after = $sql [$i+2] ?? ' ';

    if ( ( $two == '--' and ( ! $mysql or ctype_space ( $after ) or ctype_cntrl ( $after ) ) )
         or ( $mysql and $sql [$i] == '#' ) ) {
      $line = strpos ( $sql, "\n", $i );
      return ( $line === FALSE ) ? strlen ( $sql ) : $line;
    }

    return 0;

  }

  // A NULL is an empty string here, as padEscape has it; an array inside quotes is its
  // values joined with commas. MySQL escapes with backslashes; SQLite knows no backslash
  // escape - a quote is doubled, the one the literal is written in - so the MySQL form
  // there would end the literal early. A NUL ends an SQLite statement and is dropped.

  function padDbEscape ( $connect, $value, $quote = "'" ) {

    if ( is_array ( $value ) )
      $value = implode ( ',', $value );

    $value = (string) ( $value ?? '' );

    if ( $connect instanceof PDO )
      return str_replace ( [ "\0", $quote ], [ '', $quote . $quote ], $value );

    return mysqli_real_escape_string ( $connect, $value );

  }

  function padDbLiteral ( $connect, $value ) {

    if ( is_array ( $value ) )
      return count ( $value )
           ? implode ( ',', array_map ( fn ( $one ) => padDbLiteral ( $connect, $one ), $value ) )
           : 'NULL';

    if ( $value === NULL )
      return 'NULL';

    if ( is_bool ( $value ) )
      return $value ? '1' : '0';

    if ( is_int ( $value ) or is_float ( $value ) or preg_match ( '/^-?\d+(\.\d+)?$/', (string) $value ) )
      return (string) $value;

    return "'" . padDbEscape ( $connect, $value, "'" ) . "'";

  }

?>