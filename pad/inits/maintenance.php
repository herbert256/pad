<?php

  // An application that is down for maintenance - pad down, lib/maintenance.php - answers
  // 503 here, before the page cache could answer and before any of the application runs;
  // the holder of the secret is let through or, asking ?<secret>, given the cookie that
  // lets the browser through. The application's error page for 503 needs the root level to
  // render in, so it is opened first - only for a request that ends here.

  $padMaintenanceDown = padMaintenance ();

  if ( $padMaintenanceDown ) {

    if ( padErrorPageFile ( 503 ) )
      include PAD . 'inits/level.php';

    padMaintenanceRefuse ( $padMaintenanceDown );

  }

?>
