<?php

  // Snapshots the per-level arrays that carry a tag's data and settings - padData, padCurrent,
  // padSetLvl, padSetOcc, padPrm and padOpt - into $padStrSav[$padStrCnt] before an isolated
  // nested pass, for start/end/dat.php to restore.
  //
  // Only the levels below the current one are taken, since those are the ones a sandboxed
  // pass must not be able to reach up into; the level the pass itself opens is set up fresh
  // by level/setup.php.

  global $padStrSav;

  for ( $padStrIdx = 0; $padStrIdx < $pad ; $padStrIdx++ )
    foreach ( padStrDat as $padStrVal )
      $padStrSav [$padStrCnt] [$padStrVal] [$padStrIdx] = $GLOBALS [$padStrVal] [$padStrIdx];

?>