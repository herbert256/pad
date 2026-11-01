<?php

  // The boot of _client.mjs: editors/render.php's, with the page's tidy off - so the SVG the
  // server draws stands as it is drawn, to compare with the browser's - and the error action
  // boot, so a failing page answers its JSON report.
  //
  //   php _client.php <app> <page> [-]      - : the template from stdin, in place of the page's

  $padHome = getenv ( 'PAD_HOME' ) ?: dirname ( __DIR__, 4 );
  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  $padSetConfig = [ 'ErrorAction' => 'boot', 'Tidy' => FALSE ];

  $_SERVER ['REQUEST_METHOD'] = 'GET';

  $padApp  = $argv [1];
  $padPage = $argv [2];

  if ( ( $argv [3] ?? '' ) == '-' )
    $padPageSource = file_get_contents ( 'php://stdin' );

  include "$padHome/pad/pad.php";

?>
