<?php

  // Snapshots the engine's own globals into $padStrZZZ[$padStrCnt] before a nested pass, for
  // start/end/pad.php to restore afterwards. Taken for every pass, isolated or not, because
  // any pass moves the engine's position - which page, which level, which output.
  //
  // padStrPad() picks the pad/pq globals that belong to this snapshot, leaving out the level
  // arrays and the stores, which travel separately in start/start/dat.php and
  // start/start/stores.php, and the padStr* flags, which travel in start/start/start.php.

  global $padStrZZZ;

  $padStrZZZ [$padStrCnt] = [];

  foreach ( $GLOBALS as $padStrKey => $padStrVal )
    if ( padStrPad ( $padStrKey ) )
      $padStrZZZ [$padStrCnt] [$padStrKey] = $padStrVal;

?>