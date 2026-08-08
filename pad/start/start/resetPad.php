<?php

  // Gives a sandboxed or reset pass its blank slate on the engine side: empties the per-level
  // arrays of every level below the current one, so nothing inside the pass can look up into
  // the tags it is nested in, empties the four stores listed in padStrSto, and drops the
  // database handle so the pass opens its own connection under its own config.
  //
  // Everything cleared here was snapshotted a moment earlier by start/start/dat.php and
  // start/start/stores.php, and is restored by start/pad/end.php.

  for ( $padStrIdx = 0; $padStrIdx < $pad ; $padStrIdx++ )
    foreach ( padStrDat as $padStrVal )
      $GLOBALS [$padStrVal] [$padStrIdx] = [];

  foreach ( padStrSto as $padStrVal )
    if ( isset ( $GLOBALS [$padStrVal] ))
      $GLOBALS [$padStrVal] = [];

  // Through $GLOBALS, not a bare unset: this runs inside the pass's function scope, where a
  // bare unset only detaches the local alias and db() would keep finding the old handle.

  unset ( $GLOBALS ['padSqlConnect'] );

?>