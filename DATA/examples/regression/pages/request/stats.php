<?php

  // ?padStats on a request this machine makes to itself turns the stats mode on for that
  // request, which answers its timings in a PAD-Stats header - what develop/benchmark reads.
  // A visitor's request cannot switch it (padSelfSwitch).

  $padCurlStats = TRUE;

  $statsCurl = padCurl ( $padGoExt . 'request/router_dir&padInclude&padStats' );
  $statsKeys = implode ( ',', array_keys ( $statsCurl ['stats'] ?? [] ) );

?>
