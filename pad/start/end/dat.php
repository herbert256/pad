<?php

  // Restores the per-level arrays snapshotted by start/start/dat.php after an isolated nested
  // pass, for every level below the current one.
  //
  // Copying $padData back is not enough on its own: the enclosing loops read their current row
  // through PHP's internal array pointer, which the copy resets to the first element, so each
  // level's pointer is walked forward again until it sits on the key that level was on
  // ($padKey), leaving the interrupted iteration exactly where it was.

  for ( $padStrIdx = 0; $padStrIdx < $pad; $padStrIdx++ ) {

    foreach ( padStrDat as $padStrVal )
      $GLOBALS [$padStrVal] [$padStrIdx] = $padStrSav [$padStrCnt] [$padStrVal] [$padStrIdx];

    reset ( $padData [$padStrIdx] );

    while ( current ( $padData [$padStrIdx] ) !== false and
            key ( $padData [$padStrIdx] ) != $padKey [$padStrIdx] )
      next ( $padData [$padStrIdx] );

  }

?>