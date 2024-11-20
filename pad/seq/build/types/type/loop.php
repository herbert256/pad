<?php

  $padSeqGo = $padSeqStart;

  while ( $padSeqGo <= $padSeqEnd ) {

    $padSeqLoop = $padSeqGo;

    $padSeqTmp = include 'seq/build/one.php';
    if ( $padSeqTmp === FALSE )
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>