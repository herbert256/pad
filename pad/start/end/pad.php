<?php

  // Writes the engine globals snapshotted by start/start/pad.php back over $GLOBALS, putting
  // the engine back where it was - same page, same level, same output - after a nested pass
  // has moved it. Done for every pass, isolated or not.

  foreach ( $padStrZZZ [$padStrCnt] as $padStrKey => $padStrVal )
    $GLOBALS [$padStrKey] = $padStrVal;

?>