<?php

  $padSeqGo = $padSeqStart;

  while ( $padSeqGo <= $padSeqEnd ) {

    $padSeqLoop = $padSeqGo;

    if ( ! include '/pad/seq/one.php')
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>