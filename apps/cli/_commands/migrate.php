<?php

  // pad migrate <app>: the migrations of the application's _migrations/ run against its
  // database (pad/lib/migrate.php) - the database its .env and _config/config.php name, as
  // a page of it would reach it through db ().
  //
  //   pad migrate shop                    the pending migrations, as one batch
  //   pad migrate shop --pretend          the statements they would send, nothing run
  //   pad migrate shop --status           every migration: ran or not, and its batch
  //   pad migrate shop --rollback[=2]     the down of the last batch (or two)
  //   pad migrate shop --fresh --force    every table dropped, then all migrations from nothing
  //   pad migrate shop --seed             the seeders of _seeds/ after migrating (as pad seed)
  //   pad migrate shop --new=create_orders [--php]
  //                                       a new migration, stamped with the time now:
  //                                       _migrations/2026_10_08_142501_create_orders.sql
  //
  // A failure prints its message - the file and the statement - and ends with status 1;
  // the migrations before it stay done.

  $migrateArgs  = array_slice ( $argv, 2 );
  $migrateWords = array_values ( array_filter ( $migrateArgs, fn ( $one ) => ! str_starts_with ( $one, '--' ) ) );
  $migrateOpts  = [];

  foreach ( $migrateArgs as $migrateArg )
    if ( preg_match ( '/^--([a-z]+)(?:=(.*))?$/', $migrateArg, $m ) )
      $migrateOpts [ $m [1] ] = $m [2] ?? TRUE;
    elseif ( str_starts_with ( $migrateArg, '--' ) )
      return cliFail ( "migrate: there is no option '$migrateArg' - pad help" );

  foreach ( array_keys ( $migrateOpts ) as $migrateOpt )
    if ( ! in_array ( $migrateOpt, [ 'status', 'rollback', 'fresh', 'force', 'pretend', 'seed', 'new', 'php' ] ) )
      return cliFail ( "migrate: there is no option '--$migrateOpt' - pad help" );

  $migrateApp = trim ( $migrateWords [0] ?? '', '/' );

  if ( ! cliApp ( $migrateApp ) or count ( $migrateWords ) > 1 )
    return cliFail ( "there is no application named '$migrateApp' - pad migrate <app> [--status|--rollback[=n]|--fresh --force|--pretend|--seed|--new=name]" );

  if ( isset ( $migrateOpts ['fresh'] ) and ! isset ( $migrateOpts ['force'] ) )
    return cliFail ( "migrate: --fresh drops every table of $migrateApp's database - add --force to do it" );

  // --new needs no database: a file is written, that is all.

  if ( isset ( $migrateOpts ['new'] ) )
    return migrateNew ( $migrateApp, (string) $migrateOpts ['new'], isset ( $migrateOpts ['php'] ) );

  $migrateTask = function () use ( $migrateApp, $migrateOpts ) {

    $GLOBALS ['padMigrateQuiet'] = TRUE;

    $pretend = isset ( $migrateOpts ['pretend'] );

    if ( isset ( $migrateOpts ['status'] ) ) {

      $rows = padMigrateStatus ();

      if ( $rows === FALSE )
        return cliFail ( padMigrateError () );

      if ( ! $rows ) {
        cliOut ( "$migrateApp has no migrations - they go in apps/$migrateApp/_migrations/" );
        return 0;
      }

      cliOut ( '  ran  batch  migration' );

      foreach ( $rows as $row )
        cliOut ( sprintf ( '  %-3s  %-5s  %s', $row ['ran'] ? 'yes' : 'no', $row ['batch'], $row ['migration'] ) );

      return 0;

    }

    if ( isset ( $migrateOpts ['rollback'] ) ) {

      $steps = ( $migrateOpts ['rollback'] === TRUE ) ? 1 : $migrateOpts ['rollback'];
      $done  = padMigrateRollback ( $steps, $pretend );

      if ( $done === FALSE )
        return cliFail ( padMigrateError () );

      return migrateShow ( $done, $pretend, 'rolled back', 'nothing to roll back' );

    }

    $done = isset ( $migrateOpts ['fresh'] ) ? padMigrateFresh ( TRUE ) : padMigrate ( $pretend );

    if ( $done === FALSE )
      return cliFail ( padMigrateError () );

    migrateShow ( $done, $pretend, 'migrated', 'nothing to migrate' );

    if ( ! isset ( $migrateOpts ['seed'] ) or $pretend )
      return 0;

    $seeds = padSeed ();

    if ( $seeds === FALSE )
      return cliFail ( padMigrateError () );

    foreach ( $seeds as $seed )
      cliOut ( "  seeded      $seed" );

    return 0;

  };

  include cliTask ( $migrateApp, $migrateTask );

  return 1;


  // What ran, or with --pretend what would have: each name, and under it its statements.

  function migrateShow ( $done, $pretend, $verb, $none ) {

    if ( ! $done ) {
      cliOut ( $none );
      return 0;
    }

    if ( ! $pretend ) {
      foreach ( $done as $name )
        cliOut ( sprintf ( '  %-11s %s', $verb, $name ) );
      return 0;
    }

    foreach ( $done as $name => $statements ) {
      cliOut ( "-- $name" );
      foreach ( $statements as $statement )
        cliOut ( rtrim ( $statement, "; \n" ) . ';' );
      cliOut ( '' );
    }

    return 0;

  }

  // --new=name: an empty migration stamped with the time now, a .sql file - or with --php
  // one that returns its up and down. A name another migration has already is refused, so
  // two files never do the same under one name.

  function migrateNew ( $app, $name, $php ) {

    $name = trim ( preg_replace ( '/[^a-z0-9]+/', '_', strtolower ( $name ) ), '_' );

    if ( $name === '' )
      return cliFail ( 'migrate: --new=name names the migration, like --new=create_orders' );

    $dir = cliHome () . "/apps/$app/_migrations/";

    foreach ( is_dir ( $dir ) ? scandir ( $dir ) : [] as $file )
      if ( preg_match ( '/^\d{4}_\d{2}_\d{2}_\d{6}_' . $name . '\.(sql|php)$/D', $file ) )
        return cliFail ( "migrate: $app has a migration $name already - _migrations/$file" );

    if ( ! is_dir ( $dir ) and ! mkdir ( $dir, 0755, TRUE ) )
      return cliFail ( "migrate: apps/$app/_migrations/ could not be made" );

    $stamp = date ( 'Y_m_d_His' ) . "_$name";
    $file  = $dir . $stamp . ( $php ? '.php' : '.sql' );

    $text = $php

      ? "<?php\n\n  // $name: 'up' makes the change, 'down' takes it back - each SQL text, split on ;,\n"
      . "  // or a function that does its work with db ().\n\n"
      . "  return [\n\n    'up'   => \"\",\n\n    'down' => \"\",\n\n  ];\n\n?>\n"

      : "-- $name: the change, statements ended by ;. Its way back, when it has one, is the\n"
      . "-- file $stamp.down.sql beside this one.\n\n";

    if ( file_put_contents ( $file, $text ) === FALSE )
      return cliFail ( "migrate: apps/$app/_migrations/" . basename ( $file ) . ' could not be written' );

    cliOut ( "apps/$app/_migrations/" . basename ( $file ) );

    return 0;

  }

?>
