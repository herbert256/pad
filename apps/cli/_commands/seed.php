<?php

  // pad seed <app> [name]: the seeders of the application's _seeds/ - plain PHP files that
  // fill its database with db () and padFactory (pad/lib/migrate.php, pad/lib/fake.php) -
  // run in name order, or the one named: pad seed shop users runs _seeds/users.php, or
  // _seeds/02_users.php. A seeder that fails, or returns FALSE, ends the run with status 1.

  $seedApp  = trim ( $argv [2] ?? '', '/' );
  $seedName = $argv [3] ?? '';

  if ( ! cliApp ( $seedApp ) or count ( $argv ) > 4 )
    return cliFail ( "there is no application named '$seedApp' - pad seed <app> [name]" );

  $seedTask = function () use ( $seedName ) {

    $GLOBALS ['padMigrateQuiet'] = TRUE;

    $done = padSeed ( $seedName );

    if ( $done === FALSE )
      return cliFail ( padMigrateError () );

    if ( ! $done )
      cliOut ( 'nothing to seed - the seeders go in _seeds/' );

    foreach ( $done as $seed )
      cliOut ( "  seeded      $seed" );

    return 0;

  };

  include cliTask ( $seedApp, $seedTask );

  return 1;

?>
