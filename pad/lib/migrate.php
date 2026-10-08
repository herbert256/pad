<?php

  // Database migrations and seeders: the application's tables written down as a series of
  // changes, kept beside its pages and run in order on every copy of its database - the
  // developer's, the test server's, production's - so each one is built the same way.
  //
  //   _migrations/2026_10_01_120000_create_orders.sql        forward, statements split on ;
  //   _migrations/2026_10_01_120000_create_orders.down.sql   its way back (optional)
  //   _migrations/2026_10_02_090000_add_status.php           return [ 'up' => ..., 'down' => ... ]
  //   _seeds/01_users.php                                     plain PHP with db () and padFactory
  //
  //   padMigrate ();                    the pending migrations, in name order, as one batch
  //   padMigrateStatus ();              a row per migration: migration, ran, batch
  //   padMigrateRollback ( 2 );         the down of the last two batches, newest first
  //   padMigrateFresh ( TRUE );         every table dropped, then padMigrate () - forced only
  //   padSeed ();  padSeed ( 'users' ); the _seeds/ files in name order, or the one named
  //
  // padMigrate          runs what has not run yet, in name order, as one batch - the
  //                     number after the highest one recorded. Each migration runs in a
  //                     transaction of its own with its row in pad_migrations, so on SQLite a
  //                     migration that fails leaves nothing behind; on MySQL every CREATE,
  //                     ALTER and DROP commits by itself (the server's rule, not PAD's), so a
  //                     migration that fails halfway keeps the statements before the failure -
  //                     one change per migration is the way to write them there. The first
  //                     failure stops the run, the error naming the file and the statement;
  //                     the migrations before it stay done. $pretend runs nothing and answers
  //                     [ migration => [ statements ] ]: a PHP migration's function is called,
  //                     and the db () statements it makes are collected instead of sent
  // padMigrateStatus    every migration of _migrations/ - and a recorded one whose file is
  //                     gone - as rows [ migration, ran, batch ], in name order
  // padMigrateRollback  the down of every migration of the last $steps batches, newest
  //                     first, each in its transaction with its row removed; a .sql migration
  //                     goes back through its .down.sql sibling, a .php one through 'down' -
  //                     without one the rollback stops there
  // padMigrateFresh     drops every table and view of the application database and migrates
  //                     from nothing: refused unless $force is TRUE, as it throws away the data
  // padMigrateError     the message of the last failure of the functions above, '' when the
  //                     last call went well
  // padSeed             the seeders of _seeds/ - every .php file in name order, or the one
  //                     named, by its file name or by the name after a 01_ in front - each in
  //                     a scope of its own (page variables through $GLOBALS); one that fails
  //                     or returns FALSE stops the run. The names that ran
  //
  // A migration is a file of _migrations/ in the application's root, named
  // YYYY_MM_DD_HHMMSS_name.sql or .php - name in lower case, digits and _ - so its name sorts
  // by its time; any other file there is an error, which keeps a misnamed migration from
  // being skipped without a word. A .sql file is split into statements on ; outside quotes
  // and comments - the body of a CREATE TRIGGER ... BEGIN ... END stays one statement, and a
  // DELIMITER line, as MySQL dumps write them, changes the separator. A .php file returns an
  // array whose 'up' and 'down' are each SQL text, split the same way, or a function, which
  // does its work with db () - a function that returns FALSE has failed.
  //
  // What ran is the table pad_migrations in the application database - migration (the
  // name, without its extension), batch, ran_at - made on first use. The statements go to
  // the connection of db () (lib/db.php), on MySQL and SQLite alike, and are kept in $_SQL;
  // a replayed request (lib/replay.php) changes no database, so all of this refuses there.
  // Runs of one application wait for each other: a lock file under DATA/migrate/<app>/.
  //
  // A failure is reported through padError, which under the 'pad' action ends the request -
  // and kept for padMigrateError. The pad command (apps/cli/_commands/migrate.php and
  // seed.php) sets $padMigrateQuiet: it prints the message itself and ends with status 1.

  function padMigrate ( $pretend = FALSE ) {

    $GLOBALS ['padMigrateError'] = '';

    $connect = padMigrateConnect ( 'padMigrate' );
    $files   = padMigrateFiles ();

    if ( ! $connect or $files === FALSE )
      return FALSE;

    return padMigrateLocked ( function () use ( $connect, $files, $pretend ) {

      if ( ! padMigrateTable ( $connect ) )
        return FALSE;

      $ran = padMigrateRan ( $connect );

      if ( $ran === FALSE )
        return FALSE;

      $batch = $ran ? max ( $ran ) + 1 : 1;
      $done  = [];

      foreach ( $files as $name => $file ) {

        if ( isset ( $ran [$name] ) )
          continue;

        $part = padMigratePart ( $file, 'up' );

        if ( $part === FALSE )
          return FALSE;

        if ( $pretend ) {
          $done [$name] = padMigratePretend ( $connect, $part );
          continue;
        }

        $record = [ "insert into pad_migrations (migration, batch, ran_at) values ({0}, {1}, {2})",
                    [ $name, $batch, padNow ( 'Y-m-d H:i:s' ) ] ];

        if ( ! padMigrateStep ( $connect, $file, $part, $record ) )
          return FALSE;

        $done [] = $name;

      }

      return $done;

    } );

  }

  function padMigrateStatus () {

    $GLOBALS ['padMigrateError'] = '';

    $connect = padMigrateConnect ( 'padMigrateStatus' );
    $files   = padMigrateFiles ();

    if ( ! $connect or $files === FALSE or ! padMigrateTable ( $connect ) )
      return FALSE;

    $ran = padMigrateRan ( $connect );

    if ( $ran === FALSE )
      return FALSE;

    $rows = [];

    foreach ( array_unique ( array_merge ( array_keys ( $files ), array_keys ( $ran ) ) ) as $name )
      $rows [$name] = [ 'migration' => $name,
                        'ran'       => isset ( $ran [$name] ),
                        'batch'     => $ran [$name] ?? '' ];

    ksort ( $rows, SORT_STRING );

    return array_values ( $rows );

  }

  function padMigrateRollback ( $steps = 1, $pretend = FALSE ) {

    $GLOBALS ['padMigrateError'] = '';

    if ( ! is_int ( $steps ) and ! ( is_string ( $steps ) and ctype_digit ( $steps ) ) or (int) $steps < 1 )
      return padMigrateFail ( "padMigrateRollback: the steps are a number of batches, 1 or more - not '"
                              . padMakeSafe ( is_scalar ( $steps ) ? (string) $steps : get_debug_type ( $steps ), 20 ) . "'" );

    $connect = padMigrateConnect ( 'padMigrateRollback' );
    $files   = padMigrateFiles ();

    if ( ! $connect or $files === FALSE )
      return FALSE;

    return padMigrateLocked ( function () use ( $connect, $files, $steps, $pretend ) {

      if ( ! padMigrateTable ( $connect ) )
        return FALSE;

      $ran = padMigrateRan ( $connect );

      if ( $ran === FALSE )
        return FALSE;

      $batches = array_values ( array_unique ( $ran ) );
      rsort ( $batches );
      $batches = array_slice ( $batches, 0, (int) $steps );

      $back = array_filter ( $ran, fn ( $batch ) => in_array ( $batch, $batches ) );

      uksort ( $back, fn ( $a, $b ) => ( $back [$b] <=> $back [$a] ) ?: strcmp ( $b, $a ) );

      $done = [];

      foreach ( array_keys ( $back ) as $name ) {

        if ( ! isset ( $files [$name] ) )
          return padMigrateFail ( "the migration $name ran, and its file is no longer in _migrations/ - its down cannot run" );

        $part = padMigratePart ( $files [$name], 'down' );

        if ( $part === FALSE )
          return FALSE;

        if ( $pretend ) {
          $done [$name] = padMigratePretend ( $connect, $part );
          continue;
        }

        if ( ! padMigrateStep ( $connect, $files [$name], $part, [ "delete from pad_migrations where migration = {0}", [ $name ] ] ) )
          return FALSE;

        $done [] = $name;

      }

      return $done;

    } );

  }

  function padMigrateFresh ( $force = FALSE ) {

    $GLOBALS ['padMigrateError'] = '';

    if ( $force !== TRUE )
      return padMigrateFail ( 'padMigrateFresh drops every table of the application database - padMigrateFresh ( TRUE ) to do it' );

    $connect = padMigrateConnect ( 'padMigrateFresh' );
    $files   = padMigrateFiles ();

    if ( ! $connect or $files === FALSE )
      return FALSE;

    return padMigrateLocked ( fn () => padMigrateDropAll ( $connect ) ? padMigrate () : FALSE );

  }

  function padMigrateError () {

    return $GLOBALS ['padMigrateError'] ?? '';

  }

  function padSeed ( $name = '' ) {

    $GLOBALS ['padMigrateError'] = '';

    $dir   = APP . '_seeds/';
    $seeds = [];

    if ( ! is_string ( $name ) or ( $name !== '' and ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_-]*$/D', $name ) ) )
      return padMigrateFail ( "padSeed: a seeder is named like 'users' or '01_users' - not '"
                              . padMakeSafe ( is_scalar ( $name ) ? (string) $name : get_debug_type ( $name ), 40 ) . "'" );

    foreach ( is_dir ( $dir ) ? scandir ( $dir ) : [] as $file )
      if ( preg_match ( '/^([A-Za-z0-9][A-Za-z0-9_-]*)\.php$/D', $file, $match ) )
        $seeds [ $match [1] ] = $dir . $file;

    ksort ( $seeds, SORT_STRING );

    if ( $name !== '' ) {

      $seeds = array_filter ( $seeds, fn ( $one ) => $one === $name or preg_replace ( '/^\d+_/', '', $one ) === $name, ARRAY_FILTER_USE_KEY );

      if ( ! $seeds )
        return padMigrateFail ( "there is no seeder named '$name' in _seeds/" );

    }

    if ( padReplaying () )
      return padMigrateFail ( 'padSeed: a replayed request changes no database' );

    $done = [];

    foreach ( $seeds as $seed => $file ) {

      $error = padMigrateCall ( static function () use ( $file ) { return include $file; } );

      if ( $error !== '' )
        return padMigrateFail ( "the seeder _seeds/$seed.php failed: $error - the seeders after it did not run" );

      $done [] = $seed;

    }

    return $done;

  }

  // The migrations of _migrations/, by name: the file, whether it is .sql or .php, and the
  // .down.sql beside a .sql one. FALSE after the fault for a file that is no migration.

  function padMigrateFiles () {

    $dir   = APP . '_migrations/';
    $files = $downs = [];

    foreach ( is_dir ( $dir ) ? scandir ( $dir ) : [] as $file ) {

      if ( $file == '.' or $file == '..' or str_starts_with ( $file, '.' ) or is_dir ( $dir . $file ) )
        continue;

      if ( ! preg_match ( '/^(\d{4}_\d{2}_\d{2}_\d{6}_[a-z0-9_]+)(\.down)?\.(sql|php)$/D', $file, $match )
           or ( $match [2] and $match [3] == 'php' ) )
        return padMigrateFail ( "the file _migrations/" . padMakeSafe ( $file, 80 )
                                . " is no migration - one is named YYYY_MM_DD_HHMMSS_name.sql or .php, with name in lower case, digits and _" );

      if ( $match [2] )
        $downs [ $match [1] ] = $dir . $file;
      elseif ( isset ( $files [ $match [1] ] ) )
        return padMigrateFail ( "the migration {$match [1]} is in _migrations/ twice, as .sql and as .php" );
      else
        $files [ $match [1] ] = [ 'name' => $match [1], 'file' => $dir . $file, 'kind' => $match [3], 'down' => '' ];

    }

    foreach ( $downs as $name => $down )
      if ( ! isset ( $files [$name] ) or $files [$name] ['kind'] != 'sql' )
        return padMigrateFail ( "the file _migrations/$name.down.sql belongs to no migration $name.sql" );
      else
        $files [$name] ['down'] = $down;

    ksort ( $files, SORT_STRING );

    return $files;

  }

  // The up or the down of a migration: SQL text, or a function. FALSE after the fault.

  function padMigratePart ( $file, $which ) {

    $short = '_migrations/' . basename ( $file ['file'] );

    if ( $file ['kind'] == 'sql' ) {

      if ( $which == 'up' )
        return (string) file_get_contents ( $file ['file'] );

      if ( $file ['down'] === '' )
        return padMigrateFail ( "the migration $short has no way back - its down is a file {$file ['name']}.down.sql beside it" );

      return (string) file_get_contents ( $file ['down'] );

    }

    $migration = ( static function ( $padMigrateFile ) { return include $padMigrateFile; } ) ( $file ['file'] );

    if ( ! is_array ( $migration ) or ! array_key_exists ( 'up', $migration ) )
      return padMigrateFail ( "the migration $short returns no [ 'up' => ..., 'down' => ... ]" );

    if ( ! array_key_exists ( $which, $migration ) )
      return padMigrateFail ( "the migration $short has no way back - it returns no 'down'" );

    $part = $migration [$which];

    if ( ! is_string ( $part ) and ! ( $part instanceof Closure ) )
      return padMigrateFail ( "the '$which' of the migration $short is SQL text or a function - not " . get_debug_type ( $part ) );

    return $part;

  }

  // One migration's up or down, and its row in pad_migrations, in one transaction.

  function padMigrateStep ( $connect, $file, $part, $record ) {

    $short = '_migrations/' . basename ( $file ['file'] );

    padMigrateTransaction ( $connect, 'begin' );

    if ( $part instanceof Closure ) {

      $error = padMigrateCall ( $part );

      if ( $error !== '' ) {
        padMigrateTransaction ( $connect, 'rollback' );
        return padMigrateFail ( "the migration $short failed: $error" );
      }

    } else

      foreach ( padMigrateSplit ( $part, ! ( $connect instanceof PDO ) ) as $number => $statement ) {

        $error = padMigrateExec ( $connect, $statement );

        if ( $error !== '' ) {
          padMigrateTransaction ( $connect, 'rollback' );
          return padMigrateFail ( "the migration $short failed at statement " . ( $number + 1 ) . ": $error / "
                                  . padMakeSafe ( preg_replace ( '/\s+/', ' ', $statement ), 120 ) );
        }

      }

    $error = padMigrateExec ( $connect, padDbPlaceholders ( $connect, $record [0], $record [1] ) );

    if ( $error !== '' ) {
      padMigrateTransaction ( $connect, 'rollback' );
      return padMigrateFail ( "the migration $short ran, and pad_migrations could not record it: $error" );
    }

    padMigrateTransaction ( $connect, 'commit' );

    return TRUE;

  }

  // Calls a migration's function or a seeder: '' when it went well, else why not. For the
  // pad command ($padMigrateQuiet) padError and PHP's warnings throw meanwhile, as they do
  // in a queue job (lib/queue.php), so a failing db () is a message naming the file and
  // not the engine's error report; in a page they are reported the way they always are.

  function padMigrateCall ( $work ) {

    global $padEventErrorBusy;

    if ( ! ( $GLOBALS ['padMigrateQuiet'] ?? FALSE ) )
      return ( $work () === FALSE ) ? 'it returned FALSE' : '';

    $busy              = $padEventErrorBusy ?? FALSE;
    $padEventErrorBusy = TRUE;

    set_error_handler ( 'padErrorThrow' );

    try {

      return ( $work () === FALSE ) ? 'it returned FALSE' : '';

    } catch ( Throwable $e ) {

      return preg_replace ( '/^PAD: /', '', $e->getMessage () ) . ' (' . basename ( $e->getFile () ) . ':' . $e->getLine () . ')';

    } finally {

      restore_error_handler ();

      $padEventErrorBusy = $busy;

    }

  }

  // What a part would send: SQL text split into its statements, a function's db () calls
  // collected by db () itself while $padMigratePretend is set - each with its placeholders
  // filled - and answered as if they found nothing.

  function padMigratePretend ( $connect, $part ) {

    if ( ! ( $part instanceof Closure ) )
      return padMigrateSplit ( $part, ! ( $connect instanceof PDO ) );

    $GLOBALS ['padMigratePretend'] = [];

    try {
      $part ();
    } finally {
      $statements = $GLOBALS ['padMigratePretend'];
      unset ( $GLOBALS ['padMigratePretend'] );
    }

    return $statements;

  }

  // Called by db () while a pretend run calls a PHP migration's function: the statement is
  // kept, not sent, and db () answers the empty shape of its verb.

  function padMigratePretendDb ( $sql, $vars ) {

    $connect = padDbApp ();

    $GLOBALS ['padMigratePretend'] [] = ( count ( $vars ) and $connect ) ? padDbPlaceholders ( $connect, $sql, $vars ) : $sql;

    $verb = strtolower ( (string) strtok ( ltrim ( $sql ), " \t\r\n" ) );

    if ( $verb == 'field' )                                         return '';
    if ( in_array ( $verb, [ 'record', 'array', 'select' ] ) ) return [];

    return 0;

  }

  // One statement on the connection: '' when it ran, else the driver's message.

  function padMigrateExec ( $connect, $sql ) {

    $GLOBALS ['_SQL'] [] = $sql;

    return ( padDbRun ( $connect, $sql, 'migrate' ) === FALSE ) ? padDbError ( $connect ) : '';

  }

  // A transaction where the driver has one: PDO's, or mysqli's - on MySQL a CREATE, ALTER or
  // DROP commits whatever came before it, which the server does by itself.

  function padMigrateTransaction ( $connect, $what ) {

    if ( $connect instanceof PDO ) {
      if     ( $what == 'begin'  and ! $connect->inTransaction () ) $connect->beginTransaction ();
      elseif ( $what == 'commit' and   $connect->inTransaction () ) $connect->commit ();
      elseif ( $what == 'rollback' and $connect->inTransaction () ) $connect->rollBack ();
      return;
    }

    if     ( $what == 'begin'    ) @mysqli_begin_transaction ( $connect );
    elseif ( $what == 'commit'   ) @mysqli_commit            ( $connect );
    elseif ( $what == 'rollback' ) @mysqli_rollback          ( $connect );

  }

  // Splits SQL text into its statements on the separator - ; until a DELIMITER line names
  // another - outside quoted text and comments. A comment is left out, except MySQL's
  // executable /*! ... */. Within CREATE TRIGGER the separators between BEGIN and its END
  // belong to the body (a CASE ... END inside it counted along). $mysql: a backslash escapes
  // inside quotes, # starts a comment and -- wants white space after it.

  function padMigrateSplit ( $sql, $mysql ) {

    $sql     = str_replace ( "\r\n", "\n", (string) $sql );
    $len     = strlen ( $sql );
    $list    = [];
    $now     = '';
    $quote   = '';
    $delim   = ';';
    $trigger = FALSE;
    $depth   = 0;

    for ( $i = 0; $i < $len; $i++ ) {

      $char = $sql [$i];

      if ( $quote ) {

        $now .= $char;

        if ( $mysql and $char == '\\' and $i + 1 < $len )
          $now .= $sql [++$i];
        elseif ( $char == $quote and ( $sql [$i+1] ?? '' ) == $quote )
          $now .= $sql [++$i];
        elseif ( $char == $quote )
          $quote = '';

        continue;

      }

      if ( trim ( $now ) === '' and preg_match ( '/\G[ \t]*DELIMITER[ \t]+(\S+)[ \t]*(\n|$)/Ai', $sql, $match, 0, $i ) ) {
        $delim = $match [1];
        $now   = '';
        $i    += strlen ( $match [0] ) - 1;
        continue;
      }

      if ( substr ( $sql, $i, 3 ) != '/*!' and ( $end = padDbComment ( $sql, $i, $mysql ) ) ) {
        $now .= ' ';
        $i    = $end - 1;
        continue;
      }

      if ( $char == "'" or $char == '"' or $char == '`' ) {
        $quote = $char;
        $now  .= $char;
        continue;
      }

      if ( preg_match ( '/\G[A-Za-z_][A-Za-z0-9_]*/', $sql, $match, 0, $i ) ) {

        $word = strtolower ( $match [0] );

        if ( $word == 'trigger' and preg_match ( '/^\s*create\s+((temp|temporary)\s+)?$/i', $now ) )
          $trigger = TRUE;
        elseif ( $trigger and ( $word == 'begin' or $word == 'case' ) )
          $depth++;
        elseif ( $trigger and $word == 'end' )
          $depth = max ( 0, $depth - 1 );

        $now .= $match [0];
        $i   += strlen ( $match [0] ) - 1;
        continue;

      }

      if ( substr ( $sql, $i, strlen ( $delim ) ) === $delim and ! ( $trigger and $depth ) ) {

        if ( trim ( $now ) !== '' )
          $list [] = trim ( $now );

        $now     = '';
        $trigger = FALSE;
        $depth   = 0;
        $i      += strlen ( $delim ) - 1;
        continue;

      }

      $now .= $char;

    }

    if ( trim ( $now ) !== '' )
      $list [] = trim ( $now );

    return $list;

  }

  // The application's connection, FALSE after the fault when there is none.

  function padMigrateConnect ( $function ) {

    if ( padReplaying () )
      return padMigrateFail ( "$function: a replayed request changes no database" );

    $connect = padDbApp ();

    if ( ! $connect )
      return padMigrateFail ( "$function: there is no application database - the padSql settings in the application's .env name it" );

    return $connect;

  }

  function padMigrateTable ( $connect ) {

    $error = padMigrateExec ( $connect, 'create table if not exists pad_migrations ( '
                                      . 'migration varchar(255) not null primary key, '
                                      . 'batch integer not null, '
                                      . 'ran_at varchar(19) not null )' );

    return ( $error === '' ) ? TRUE : padMigrateFail ( "the table pad_migrations could not be made: $error" );

  }

  // What ran: [ migration => batch ].

  function padMigrateRan ( $connect ) {

    $sql = 'select migration, batch from pad_migrations order by migration';

    $GLOBALS ['_SQL'] [] = $sql;

    $run = padDbRun ( $connect, $sql, 'array' );

    if ( $run === FALSE )
      return padMigrateFail ( 'pad_migrations could not be read: ' . padDbError ( $connect ) );

    $ran = [];

    foreach ( $run ['all'] as $row )
      $ran [ $row ['migration'] ] = (int) $row ['batch'];

    return $ran;

  }

  // Every view and table of the application database dropped, foreign keys aside meanwhile.

  function padMigrateDropAll ( $connect ) {

    $sqlite = ( $connect instanceof PDO );

    $list = $sqlite
          ? "select type, name from sqlite_master where type in ('view', 'table') and name not like 'sqlite_%' order by type desc, name"
          : "select lower(table_type) as type, table_name as name from information_schema.tables where table_schema = database() order by table_type desc, table_name";

    $run = padDbRun ( $connect, $list, 'array' );

    if ( $run === FALSE )
      return padMigrateFail ( 'the tables could not be listed: ' . padDbError ( $connect ) );

    padMigrateExec ( $connect, $sqlite ? 'PRAGMA foreign_keys = OFF' : 'SET FOREIGN_KEY_CHECKS = 0' );

    $error = '';

    foreach ( $run ['all'] as $row ) {

      $name  = $sqlite ? '"' . str_replace ( '"', '""', $row ['name'] ) . '"' : '`' . str_replace ( '`', '``', $row ['name'] ) . '`';
      $what  = str_contains ( strtolower ( $row ['type'] ), 'view' ) ? 'view' : 'table';
      $error = padMigrateExec ( $connect, "drop $what if exists $name" );

      if ( $error !== '' ) {
        $error = "the $what {$row ['name']} could not be dropped: $error";
        break;
      }

    }

    padMigrateExec ( $connect, $sqlite ? 'PRAGMA foreign_keys = ON' : 'SET FOREIGN_KEY_CHECKS = 1' );

    return ( $error === '' ) ? TRUE : padMigrateFail ( $error );

  }

  // The work under the application's lock: one run at a time, so two deploys that migrate
  // at once do not both run the same migration. Held already - padMigrateFresh holds it
  // over the drop and the migrate - the work just runs.

  function padMigrateLocked ( $work ) {

    global $padApp, $padDirMode;

    static $held = FALSE;

    if ( $held )
      return $work ();

    $file = DATA . 'migrate/' . $padApp . '/migrate.lock';

    if ( ! is_dir ( dirname ( $file ) ) )
      @mkdir ( dirname ( $file ), $padDirMode ?? 0755, TRUE );

    $lock = @fopen ( $file, 'c' );

    if ( $lock )
      flock ( $lock, LOCK_EX );

    $held = TRUE;

    try {
      return $work ();
    } finally {
      $held = FALSE;
      if ( $lock ) {
        flock ( $lock, LOCK_UN );
        fclose ( $lock );
      }
    }

  }

  // A failure: kept for padMigrateError, and reported - unless the pad command asked to
  // report it itself.

  function padMigrateFail ( $message ) {

    $GLOBALS ['padMigrateError'] = $message;

    if ( ! ( $GLOBALS ['padMigrateQuiet'] ?? FALSE ) )
      padError ( $message );

    return FALSE;

  }

?>
