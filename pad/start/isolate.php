<?php

  include pad . 'start/resetPad.php';
  include pad . 'start/resetApp.php';

  foreach ( $padSolData [$padSolCnt] ['padSetLvl'] [$pad] as $k => $v ) {
    $GLOBALS [$k] = $v;
    global $$k;
  }

?>