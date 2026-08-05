<?php

  // Writes the data, content, bool and sequence stores snapshotted by start/start/stores.php
  // back over $GLOBALS after an isolated nested pass, discarding whatever the pass pushed
  // into them.

  foreach ( $padStrStoDat [$padStrCnt] as $padStrKey => $padStrVal )
    $GLOBALS [$padStrKey] = $padStrVal;

?>