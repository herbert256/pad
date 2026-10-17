<?php

  // Snapshots the application's own globals - everything the page's PHP created - into
  // $padStrApp[$padStrCnt] before an isolated nested pass, so start/end/app.php can put them
  // back and start/end/unsetApp.php can tell which of them the pass invented.
  //
  // padAppVars() draws the line: anything not named pad*, pq* or one of PHP's superglobals
  // counts as the application's.

  global $padStrApp;

  $padStrApp [$padStrCnt] = padAppVars ( $GLOBALS );

?>
