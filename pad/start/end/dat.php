<?php

  // Restores the per-level arrays snapshotted by start/start/dat.php after an isolated nested
  // pass, for every level below the current one.
  //
  // Copying $padData back is not enough on its own: the enclosing loops read their current row
  // through PHP's internal array pointer, which the copy resets to the first element, so each
  // level's pointer is walked forward again until it sits on the key that level was on
  // ($padKey), leaving the interrupted iteration exactly where it was.
  //
  // The walk tests key(), not current(): a row whose value is FALSE is still a row. And a
  // level whose saved key is not in its data at all - a level that never started iterating
  // keeps the setup seed, which the default data does not contain - is put back at the
  // start, where an unstarted level's pointer belongs. Walking off the end instead left the
  // next occurrence of that level reading key NULL, which is how a pass run from a page's
  // own PHP made the page build fail before its first tag.

  for ( $padStrIdx = 0; $padStrIdx < $pad; $padStrIdx++ ) {

    foreach ( padStrDat as $padStrVal )
      $GLOBALS [$padStrVal] [$padStrIdx] = $padStrSav [$padStrCnt] [$padStrVal] [$padStrIdx];

    reset ( $padData [$padStrIdx] );

    while ( key ( $padData [$padStrIdx] ) !== NULL and
            key ( $padData [$padStrIdx] ) != $padKey [$padStrIdx] )
      next ( $padData [$padStrIdx] );

    if ( key ( $padData [$padStrIdx] ) === NULL )
      reset ( $padData [$padStrIdx] );

  }

?>