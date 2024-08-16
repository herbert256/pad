<?php

  $padSeqGo = $padSeqStart;

  while ( $padSeqGo <= $padSeqEnd ) {

    $padSeqLoop = $padSeqGo;

    if ( ! include '/pad/sequence/one.php')
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>