<?php

  foreach ( $padSetLvl [$pad] as $padStrKey => $padStrVal ) {
    $GLOBALS [$padStrKey] = $padStrVal;
    global $$padStrKey;
  }

?>