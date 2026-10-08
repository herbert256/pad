<?php

  // Maintenance mode for every application at once (pad/lib/maintenance.php): which are
  // down, since when and with what message; taking one down, bringing one up. The console
  // itself is never offered - down, nobody could bring it back from here.

  $title = 'Maintenance';

  if ( adminPost () ) {

    $app    = adminAppAsked ();
    $action = adminField ( 'action' );

    if ( $app === '' or $app === 'admin' or $app === '_common' )
      adminDone ( 'Choose an application.', 'maintenance', [], 'error' );

    if ( $action == 'down' ) {
      padMaintenanceDown ( $app, adminField ( 'secret' ), (int) adminField ( 'retry', '60' ), adminField ( 'message' ) );
      adminDone ( "$app is down for maintenance.", 'maintenance' );
    }

    if ( $action == 'up' ) {
      padMaintenanceUp ( $app );
      adminDone ( "$app is up again.", 'maintenance' );
    }

  }

  $downRows = [];

  foreach ( padMaintenanceList () as $name => $one )
    $downRows [] = [ 'app' => $name, 'when' => adminWhen ( $one ['time'] ), 'ago' => adminAgo ( $one ['time'] ),
                     'retry' => $one ['retry'], 'message' => $one ['message'], 'secret' => $one ['secret'] !== '' ? 1 : 0 ];

  $downCount = count ( $downRows );

  $appRows = [];

  foreach ( adminApps () as $name => $one )
    if ( $name !== '_common' and $name !== 'admin' and ! padMaintenanceRead ( $name ) )
      $appRows [] = [ 'name' => $name ];

?>
