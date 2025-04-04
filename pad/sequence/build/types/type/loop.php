<?php

  $padSeqBuildType = 'loop';
  
  $padSeqGo = $padSeqStart;

  while ( $padSeqGo <= $padSeqEnd ) {

    $padSeqLoop = $padSeqGo;

    $padSeqTmp = include 'sequence/build/one.php';
    if ( $padSeqTmp === FALSE )
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>