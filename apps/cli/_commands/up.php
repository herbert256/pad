<?php

  // pad up <app>: brings an application back from maintenance - pad down - by removing its
  // DATA/maintenance/<app>.json (pad/lib/maintenance.php). Every bypass cookie handed out
  // for that time is worthless after.

  if ( ! defined ( 'DATA' ) )
    define ( 'DATA', cliHome () . '/DATA/' );

  include_once cliHome () . '/pad/lib/maintenance.php';

  $upApp = $argv [2] ?? '';

  if ( ! cliApp ( $upApp ) or count ( $argv ) > 3 )
    return cliFail ( "there is no application named '$upApp' - pad up <app>" );

  if ( ! padMaintenanceUp ( $upApp ) )
    return cliFail ( "$upApp is not down" );

  cliOut ( "$upApp is up" );

  return 0;

?>
