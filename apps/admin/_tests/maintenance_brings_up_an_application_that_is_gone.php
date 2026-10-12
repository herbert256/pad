<?php

  // A maintenance record of an application that is no longer there - renamed, removed: it
  // was listed on the maintenance page, but bringing it up was refused as no application.

  $goneApp = 'goneapp' . getmypid ();

  padMaintenanceDown ( $goneApp );

  $_POST ['app'] = $_REQUEST ['app'] = $goneApp;

  $goneUp   = adminMaintenanceApp ( 'up' ) === $goneApp ? 'can be brought up' : 'refused';
  $goneDown = adminMaintenanceApp ( 'down' ) === '' ? 'cannot be taken down' : 'taken down';

  padMaintenanceUp ( $goneApp );

  unset ( $_POST ['app'], $_REQUEST ['app'] );

?>
