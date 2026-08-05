<?php

  // Writes the application variables snapshotted by start/start/app.php back over $GLOBALS
  // after an isolated nested pass. Run last in start/pad/end.php, so that the values the
  // calling page had win over anything the pass assigned to the same names.

  foreach ( $padStrApp [$padStrCnt] as $padStrKey => $padStrVal )
    $GLOBALS [$padStrKey] = $padStrVal;

?>