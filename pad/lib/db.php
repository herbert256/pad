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

    // A pretend migration (lib/migrate.php) sends nothing: the statement is collected.

    if ( isset ( $GLOBALS ['padMigratePretend'] ) )
      return padMigratePretendDb ( $sql, $vars );

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

    global $_SQL, $pad, $padInfo, $padPrm;

    $input = $sql;

    if ( count ( $vars ) and $padSqlConnect )
      $sql = padDbPlaceholders ( $padSqlConnect, $sql, $vars );

    // The verb ends at any white space, as SQL has it: split on a space alone, a statement
    // with a newline or a tab after its verb - a named query's select on a line of its own -
    // was no verb db() knew, so its rows were answered as '' and a record, field or check
    // went to the database as written and failed there.

    $split   = preg_split ( '/\s+/', trim ( $sql ), 2 );
    $command = strtolower ( $split [0] );
    $rest    = $split [1] ?? '';

    if ($command == 'select')
      $command = 'array';

    // A read that does not start with SELECT - a WITH, a bracketed (select ...) union
    // (select ...), SHOW, DESCRIBE, EXPLAIN, PRAGMA, VALUES - answers its rows as an array
    // does, sent as it is written: no verb took it, so it ran and its rows were dropped, and
    // db("with t as (...) select name from t") answered ''. A WITH that writes answers the
    // rows it changed, a PRAGMA that sets answers '' as before.

    $asWritten = ( in_array ( $command, [ 'with', 'show', 'describe', 'desc', 'explain', 'pragma', 'values' ] )
                   or str_starts_with ( $command, '(' ) ) ? $command : '';

    if ( $asWritten )
      $command = 'array';

    // A trailing semicolon ends a statement of one row: the limit added below went after it.
    // A comment that ends the statement is taken off first, and put back at its end: the
    // semicolon, the statement's own limit and its lock were looked for at the end of the
    // text with the comment, so "... limit 1 -- the one row" and "...; -- the one" got the
    // limit below on a line after the comment, an SQL syntax error.

    $comment = '';

    if ( in_array ( $command, [ 'check', 'record', 'field' ] ) ) {

      $cut = padDbTrailingComment ( $rest, ! ( $padSqlConnect instanceof PDO ) );

      if ( $cut !== FALSE ) {
        $comment = ' ' . trim ( substr ( $rest, $cut ) );
        $rest    = rtrim ( substr ( $rest, 0, $cut ) );
      }

      $rest = preg_replace ( '/\s*;\s*$/', '', $rest );

    }

    // A statement with nothing to run - db(''), or a verb alone: {field ''} is db('field ') -
    // is not sent. The verb alone ended the request on a PHP undefined array key, and an
    // empty statement on the ValueError mysqli_query and PDO::query throw for one.

    $empty = ( $command === '' or ( $rest === '' and in_array ( $command, [ 'check', 'record', 'field', 'array' ] ) ) );

    // One row is all a check, a record or a field reads, so the limit is added - unless the
    // statement ends in a limit of its own: "record * from orders order by date desc limit
    // 1" became "... limit 1 limit 0,1", an SQL syntax error, as did a select with
    // htmlAttrJson='record' and rows=. It goes in front of a locking clause that ends the
    // statement - FOR UPDATE, FOR SHARE, LOCK IN SHARE MODE - which MySQL wants last, and
    // on a line of its own in a statement with a line comment, which would swallow it:
    // "record name from staff where name = {0} for update" was "... for update limit 0,1",
    // a syntax error, and "check staff where salary > 0 -- any" counted every row.

    $limit = $lock = '';

    if ( in_array ( $command, [ 'check', 'record', 'field' ] ) ) {

      if ( preg_match ( '/\s(for\s+(update|share)\b[^()\'"`;]*|lock\s+in\s+share\s+mode\s*)$/i', $rest, $match, PREG_OFFSET_CAPTURE ) ) {
        $lock = $match [0] [0];
        $rest = substr ( $rest, 0, $match [0] [1] );
      }

      if ( ! preg_match ( '/\blimit\s+\d+(\s*,\s*\d+|\s+offset\s+\d+)?\s*$/i', $rest ) )
        $limit = preg_match ( '/--|#/', $rest ) ? "\n limit 0,1" : ' limit 0,1';

    }

    if     ( $command == 'check'  )  $sql = 'select 1 from ' . $rest . $limit . $lock . $comment;
    elseif ( $command == 'record' )  $sql = 'select '        . $rest . $limit . $lock . $comment;
    elseif ( $command == 'field'  )  $sql = 'select '        . $rest . $limit . $lock . $comment;
    elseif ( $command == 'array' and ! $asWritten ) $sql = 'select ' . $rest;

    $_SQL [] = $sql;

    // No connection - the connect reported why - or a failed statement: reported with the
    // statement, and db() answers the empty shape of its command, as for no rows: '' for a
    // field, [] for a record or an array, FALSE for anything else.

    $start = hrtime ( TRUE );
    $run   = ( $padSqlConnect and ! $empty ) ? padDbRun ( $padSqlConnect, $sql, $command ) : FALSE;
    $ms    = ( hrtime ( TRUE ) - $start ) / 1e6;

    if ( ! $run ) {

      if ( $empty )
        padError ( "SQL: the statement '" . padMakeSafe ( trim ( $input ), 40 ) . "' has nothing to run" );
      elseif ( $padSqlConnect )
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

        // The row itself is kept, for padDataIsRecord to know it again - its columns alone
        // made any array of the page holding those keys a record (lib/data.php).

        padDataKeepRecord ( $fields );
      }
    elseif ( $command == 'array' and $asWritten and ! $run ['set'] )
      $return = ( $asWritten == 'with' ) ? $rows : '';
    elseif ( $command == 'array'  ) {

      // Keyed by an id that can be a key: a floating point id - a PHP float since the
      // drivers answer numbers - ended the request on "Implicit conversion from float 1.5
      // to int loses precision", and 1.2 and 1.7 would have shared the key 1.

      $return = array();
      foreach ( $run ['all'] as $record )
        if ( isset($record['id']) and ( is_int ( $record ['id'] ) or is_string ( $record ['id'] ) ) and !isset($return [$record['id']]) )
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
  // row for field and record, every row for array, the new id for insert, whether the
  // statement answered rows at all - or FALSE when the statement failed.

  function padDbRun ( $connect, $sql, $command ) {

    $run = [ 'rows' => 0, 'first' => NULL, 'all' => [], 'id' => 0, 'set' => FALSE ];

    if ( $connect instanceof PDO ) {

      // SQLite's last insert id is the connection's, moved only by a row that got a new
      // rowid: an insert that inserted nothing - INSERT OR IGNORE of a duplicate, an empty
      // INSERT ... SELECT - an upsert that updated, or an insert into a WITHOUT ROWID table
      // answered the id of an earlier insert, of another table even. Unmoved, there is no
      // new id, and the insert answers its row count, as MySQL's does without one. It is
      // set to a mark no row has first (padDbSqliteMark): compared with the id as it stood,
      // a new row given the same id as the insert before it - an order and its first line,
      // an id handed out again after the last row was deleted - answered 1, its row count.

      $before = ( $command == 'insert' ) ? padDbSqliteMark ( $connect ) : '';

      $query = $connect->query ( $sql );

      if ( $query === FALSE )
        return FALSE;

      if ( $query->columnCount () ) {
        $run ['all']  = $query->fetchAll ( PDO::FETCH_ASSOC );
        $run ['rows'] = count ( $run ['all'] );
        $run ['set']  = TRUE;
      } else
        $run ['rows'] = $query->rowCount ();

      $run ['first'] = $run ['all'] [0] ?? NULL;

      if ( $command == 'insert' and $connect->lastInsertId () !== $before )
        $run ['id'] = (int) $connect->lastInsertId ();

      return $run;

    }

    $query = mysqli_query ( $connect , $sql );

    if ( ! $query )
      return FALSE;

    $run ['rows'] = mysqli_affected_rows ( $connect );
    $run ['set']  = ( $query instanceof mysqli_result );

    if ( $run ['set'] and $run ['rows'] > 0 and ( $command == 'field' or $command == 'record' ) )
      $run ['first'] = mysqli_fetch_assoc ( $query );

    if ( $run ['set'] and $run ['rows'] > 0 and $command == 'array' )
      while ( $record = mysqli_fetch_assoc ( $query ) )
        $run ['all'] [] = $record;

    if ( $command == 'insert' )
      $run ['id'] = mysqli_insert_id ( $connect );

    return $run;

  }

  // Sets the SQLite connection's last insert id to a rowid no row has - the smallest there
  // is - by putting that row in a table of the connection's own temporary database, and
  // answers it; when that cannot be done, the last insert id as it stands.

  function padDbSqliteMark ( $connect ) {

    $mark = 'insert or replace into temp.pad_insert_id (rowid) values (-9223372036854775808)';

    if ( $connect->exec ( $mark ) === FALSE and $connect->exec ( 'create temp table if not exists pad_insert_id (x)' ) !== FALSE )
      $connect->exec ( $mark );

    return $connect->lastInsertId ();

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
          if ( $padCheckSyntax and ! padStrHidden ( $name ) )
            padError ( "the named query " . basename ( $file ) . " reads {\$$name}, and there is no field named '$name'" );
          $vars [$key] = '';
        }

        return '{' . $key . '}';

      }, $sql );

    // A WITH reads when neither its main statement nor one of its named selects writes - the
    // replay's test of a statement (lib/replay.php) - and db() answers its rows. The refusal
    // names the write; it said the query "starts with 'with'", which is no reason.

    $verb = strtolower ( strtok ( ltrim ( $sql ), " \t\r\n" ) );

    if ( ! in_array ( $verb, [ 'select', 'array', 'record', 'field', 'check', 'with' ] ) )
      return padError ( "the named query " . basename ( $file ) . " must read - it starts with '" . padMakeSafe ( $verb, 20 ) . "'" );

    if ( $verb == 'with' and padReplayWrites ( $sql, $write ) )
      return padError ( "the named query " . basename ( $file ) . " must read - its WITH holds the write '$write'" );

    return db ( trim ( $sql ), $vars );

  }

  // The SQL of a database tag - {field}, {record}, {array}, {check} - is the template's own
  // text, as the select's where= is: one quoted string, a value put in as $name and bound by
  // padSelectBind as an escaped literal of that variable. The parameter was any expression,
  // so a request value concatenated in - {field "x from t where city = '" . $city . "'"} - or
  // a whole statement in a variable - {field $q} - ran the visitor's SQL, while a value put
  // in as {$city} reached the database as PAD's inert stand-ins and never matched. FALSE
  // refuses the tag.

  function padDbTagSql () {

    global $pad, $padParms, $padOpt, $padTag;

    foreach ( $padParms [$pad] ?? [] as $one )
      if ( $one ['padPrmKind'] == 'parm' and $one ['padPrmName'] === 1 ) {
        $org = trim ( (string) $one ['padPrmOrg'] );
        if ( ! is_numeric ( $org ) and ! is_string ( padMetaLiteral ( $org ) ) )
          return padError ( "the SQL of {" . $padTag [$pad] . "} is the template's own: give it as a quoted string, and put a value in it as \$name" );
      }

    // A value spliced in with {$n} is resolved before the tag reads its parameter, so the
    // literal test above never sees it: padSpliceQuote refuses one standing outside a quoted
    // literal of the SQL (padDbSplice, below), and what that cannot place - a tag's answer,
    // {echo $n}, lands bare in the SQL as well - is caught here when it was kept as a spliced
    // value (padSpliceInCode, lib/level.php) and stands outside the SQL's quoted literals.

    $sql = (string) ( $padOpt [$pad] [1] ?? '' );

    if ( padSpliceInCode ( $sql ) )
      return padError ( "the SQL of {" . $padTag [$pad] . "} is the template's own: a value spliced into it outside a quoted literal is SQL - put it in as \$name" );

    return padSelectBind ( $sql, FALSE, '{' . $padTag [$pad] . '}' );

  }

  // Whether a value spliced into a tag's text stands in SQL the template writes, outside a
  // quoted literal of that SQL - and the refusal if so, for padSpliceQuote (lib/level.php),
  // which hands it $inside, the tag's text from its { up to the splice, and the quote open
  // there with where it opened. The value is no plain number: one stands as itself.
  //
  // The SQL of a database tag - {field "x from t where id = {$n}"}, field("...") in an
  // expression, the whole of it bare, {field {$q}} - and having=, order=, group=, fields=,
  // db=, join= and union= of a declared select table took the value as SQL: ?n=0 union
  // select user() read any table, the single word ?n=id made id = id and every row came
  // back, and {field {$q}} was quoted into one string the tag took for the template's own
  // SQL. Inside a quoted literal of the SQL - name = '{$who}' - a value is text, its quotes
  // PAD's inert stand-ins. where= of a select is left to padSpliceExpression, which quotes
  // a value there as a string of the expression it takes it for.

  function padDbSplice ( $inside, $quote, $quoteAt ) {

    global $padSelect;

    $head = ( $quote === '' ) ? $inside : substr ( $inside, 0, $quoteAt );

    if ( preg_match ( '/^\s*(?:pad:)?(field|record|array|check)\s+$/i', $head, $match ) )
      $what = 'the SQL of {' . strtolower ( $match [1] ) . '} is the template\'s own';
    elseif ( preg_match ( '/(?<![\w$:.@])(field|record|array|check)\s*\(\s*$/i', $head, $match ) )
      $what = 'the SQL of ' . strtolower ( $match [1] ) . '() is the template\'s own';
    elseif ( preg_match ( '/^(select:)?([A-Za-z_][\w-]*)(\s.*)?[\s,](having|order|group|fields|db|join|union)\s*=\s*$/is', $head, $match )
             and ( $match [1] or isset ( $padSelect [$match [2]] ) ) )
      $what = strtolower ( $match [4] ) . '= is SQL the template writes';
    else
      return '';

    // fields=, db=, join= and union= bind no $name: no value goes into them at all.

    if ( isset ( $match [4] ) and in_array ( strtolower ( $match [4] ), [ 'fields', 'db', 'join', 'union' ] ) )
      return "$what: a value spliced into it is SQL";

    $refuse = "$what: a value spliced into it outside a quoted literal is SQL - put it in as \$name";

    if ( $quote === '' )
      return $refuse;

    // The SQL up to the splice, as the database reads it once the parameter's own quote is
    // unescaped: a comment is skipped as a whole, an apostrophe in it opening nothing, and a
    // backslash escapes in a literal on MySQL only (padDbComment, padDbPlaceholders).

    $sql   = str_replace ( '\\' . $quote, $quote, substr ( $inside, $quoteAt + 1 ) );
    $mysql = ( $GLOBALS ['padSqlDriver'] ?? 'mysql' ) !== 'sqlite';
    $in    = '';

    for ( $i = 0, $len = strlen ( $sql ); $i < $len; $i++ )
      if ( $in ) {
        if     ( $mysql and $sql [$i] == '\\' ) $i++;
        elseif ( $sql [$i] == $in             ) $in = '';
      } elseif ( $sql [$i] == "'" or $sql [$i] == '"' )
        $in = $sql [$i];
      elseif ( $end = padDbComment ( $sql, $i, $mysql ) )
        $i = $end - 1;

    return ( $in === '' ) ? $refuse : '';

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
  //
  // The same pass knows where a bare placeholder stands where MySQL wants a number, for
  // padDbLiteral: right after LIMIT, OFFSET and FETCH FIRST / NEXT, after the comma of
  // LIMIT n, and as an item of an ORDER BY or GROUP BY list - first, or after a comma of
  // that list at its own bracket depth. It follows the statement's words and brackets: a
  // regex over the last 128 bytes took the select list of a UNION after a GROUP BY for that
  // list ('007' became 7), missed an item after a bracketed one - order by field(...), {0}
  // - and any list longer than its window.

  function padDbPlaceholders ( $connect, $sql, $vars ) {

    $out    = '';
    $len    = strlen ( $sql );
    $quote  = '';
    $slash  = ! ( $connect instanceof PDO );
    $depth  = 0;
    $clause = [ 0 => '' ];
    $expect = FALSE;
    $word   = '';
    $dot    = FALSE;

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

      } elseif ( $char == "'" or $char == '"' or $char == '`' ) {

        $quote  = $char;
        $expect = FALSE;
        $word   = '';

      }

      if ( $char == '{' and preg_match ( '/\G\{([A-Za-z0-9_]+)(?::(\d+))?\}/', $sql, $match, 0, $i ) ) {

        $key = $match [1];

        if ( ! array_key_exists ( $key, $vars ) ) {
          $out .= $char;
          continue;
        }

        $value = $vars [$key];

        if ( isset ( $match [2] ) and is_scalar ( $value ) )
          $value = mb_substr ( (string) $value, 0, (int) $match [2] );

        if     ( $key [0] == 'x'    ) $add = is_array ( $value ) ? implode ( ',', $value ) : (string) $value;
        elseif ( $quote == '`'      ) $add = str_replace ( '`', '``', (string) $value );
        elseif ( $quote             ) $add = padDbEscape ( $connect, $value, $quote );
        else                          $add = padDbLiteral ( $connect, $value, $expect );

        // A bare negative number right after a minus - "10-{0}" with -5 - would join into
        // "10--5", which SQLite reads as "10" followed by a -- line comment that swallows
        // the rest of the statement (MySQL wants white space after --, so it computed
        // 10 - -5 = 15): the drivers disagreed and SQLite gave the wrong answer or an
        // "incomplete input" error. A space keeps the two minuses apart - outside quotes
        // only: inside a quoted literal the value is text, and 'A-{0}' with -1 is A--1.

        if ( $quote === '' and $add !== '' and $add [0] === '-' and $out !== '' and $out [ strlen ( $out ) - 1 ] === '-' )
          $add = " $add";

        $out .= $add;

        if ( ! $quote ) {
          $expect = FALSE;
          $word   = '';
        }

        $i += strlen ( $match [0] ) - 1;
        continue;

      }

      // Outside quotes the words and brackets move the clause along. A word is read whole,
      // and only an ASCII letter or _ starts one: ctype_alpha follows the locale, which took
      // the first byte of naïef for a letter, the word pattern then matched nothing, and the
      // scan stood still - a 500 at once, and an endless loop under the ignore and log
      // actions. Any other byte goes on as a single character.

      if ( ! $quote and $char != "'" and $char != '"' and $char != '`' ) {

        if ( preg_match ( '/\G[A-Za-z_][A-Za-z0-9_$]*/', $sql, $match, 0, $i ) ) {

          $one = strtolower ( $match [0] );

          // A word after a dot is a name - h.offset is a column - and changes no clause: it
          // made the clause a limit, and a ', {0}' after it a number. ROWS and RANGE open a
          // window frame, whose bounds - after ROWS / RANGE, BETWEEN and AND - are numbers
          // too: ROWS BETWEEN '1' PRECEDING was "Integer is required for ROWS-type frame".

          if     ( $dot )                                                        $expect = FALSE;
          elseif ( $one == 'by' and ( $word == 'order' or $word == 'group' ) )   { $clause [$depth] = 'by';    $expect = TRUE; }
          elseif ( $one == 'limit' or $one == 'offset' )                         { $clause [$depth] = 'limit'; $expect = TRUE; }
          elseif ( ( $one == 'first' or $one == 'next' ) and $word == 'fetch' ) { $clause [$depth] = 'limit'; $expect = TRUE; }
          elseif ( $one == 'rows' or $one == 'range' )                           { $clause [$depth] = 'frame'; $expect = TRUE; }
          elseif ( ( $one == 'between' or $one == 'and' ) and ( $clause [$depth] ?? '' ) == 'frame' ) $expect = TRUE;
          else {
            $expect = FALSE;
            if ( in_array ( $one, [ 'select', 'union', 'intersect', 'except', 'from', 'where', 'having',
                                    'window', 'row', 'for', 'into', 'lock', 'returning' ] ) )
              $clause [$depth] = '';
          }

          $dot  = FALSE;
          $word = $one;
          $out .= $match [0];
          $i   += strlen ( $match [0] ) - 1;
          continue;

        }

        if     ( $char == '(' )         { $clause [++$depth] = ''; $expect = FALSE; $word = ''; }
        elseif ( $char == ')' )         { $depth = max ( 0, $depth - 1 ); $expect = FALSE; $word = ''; }
        elseif ( $char == ',' )         { $expect = in_array ( $clause [$depth] ?? '', [ 'by', 'limit' ] ); $word = ''; }
        elseif ( ! ctype_space ( $char ) ) { $expect = FALSE; $word = ''; }

        if ( ! ctype_space ( $char ) )
          $dot = ( $char == '.' );

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

  // Where the comments that end a statement start - after that offset there are comments and
  // white space only - or FALSE when it does not end in a comment. Quoted text is skipped,
  // with a backslash escape on MySQL ($mysql), as padDbPlaceholders reads it.

  function padDbTrailingComment ( $sql, $mysql ) {

    // A statement with no comment opener in it has no trailing comment: walked byte by byte
    // all the same, every RECORD, FIELD and CHECK paid for its length - a 229 KB FIELD eight
    // times its own time.

    if ( ! str_contains ( $sql, '--' ) and ! str_contains ( $sql, '/*' ) and ! ( $mysql and str_contains ( $sql, '#' ) ) )
      return FALSE;

    $quote = '';
    $cut   = FALSE;
    $len   = strlen ( $sql );

    for ( $i = 0; $i < $len; $i++ ) {

      $char = $sql [$i];

      if ( $quote ) {
        if     ( $mysql and $char == '\\' ) $i++;
        elseif ( $char == $quote          ) $quote = '';
        continue;
      }

      if ( $end = padDbComment ( $sql, $i, $mysql ) ) {
        if ( $cut === FALSE )
          $cut = $i;
        $i = $end - 1;
        continue;
      }

      if ( ctype_space ( $char ) )
        continue;

      $cut = FALSE;

      if ( $char == "'" or $char == '"' or $char == '`' )
        $quote = $char;

    }

    return $quote ? FALSE : $cut;

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

  // A number is a number. Text that looks like one - every request value arrives as text -
  // is a quoted literal on MySQL, which compares a text column with a number as a number:
  // the documented name = {0} with '0' matched every name that does not start with a digit,
  // and '01234' lost its zero. Where MySQL wants a number ($number, padDbPlaceholders) -
  // a count after LIMIT, OFFSET or FETCH, a column position in an ORDER BY or GROUP BY list
  // - it takes no quoted literal, so there numeric text is the number it says. SQLite
  // compares a text column with a number as text, and a quoted '3' against count(*) is
  // never equal, so it keeps the number - but only a numeral SQLite gives back as the same
  // text (padDbNumeral): 007 is the integer 7, so the code '007' found the row of code '7'
  // and the zip '01234' was stored as '1234'.

  function padDbLiteral ( $connect, $value, $number = FALSE ) {

    if ( is_array ( $value ) )
      return count ( $value )
           ? implode ( ',', array_map ( fn ( $one ) => padDbLiteral ( $connect, $one, $number ), $value ) )
           : 'NULL';

    if ( $value === NULL )
      return 'NULL';

    if ( is_bool ( $value ) )
      return $value ? '1' : '0';

    if ( is_int ( $value ) or is_float ( $value ) )
      return (string) $value;

    if ( $number and preg_match ( '/^-?\d+(\.\d+)?$/', (string) $value ) )
      return (string) $value;

    if ( $connect instanceof PDO and padDbNumeral ( (string) $value ) )
      return (string) $value;

    return "'" . padDbEscape ( $connect, $value, "'" ) . "'";

  }

  // Numeric text that reads back as itself once it is a number: 42, -7, 1.5 - not 007,
  // 01234, 1.10, -0 or a number too long for an integer, which SQLite would store and
  // compare as another text.

  function padDbNumeral ( $text ) {

    if ( preg_match ( '/^-?[0-9]+$/', $text ) )
      return (string) (int) $text === $text;

    if ( preg_match ( '/^-?[0-9]+\.[0-9]+$/', $text ) )
      return (string) (float) $text === $text;

    return FALSE;

  }

?>
