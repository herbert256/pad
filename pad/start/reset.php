<?php

  include pad . 'start/resetPad.php';
  include pad . 'start/resetApp.php';

  foreach ( $padStrDat [$padStrCnt] ['padSetLvl'] [$pad] as $k => $v ) {
    $GLOBALS [$k] = $v;
    global $$k;
  }

?>