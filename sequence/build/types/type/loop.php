<?php

  $padSeqGo = $padSeqStart;

  while ( $padSeqGo <= $padSeqEnd ) {

    $padSeqLoop = $padSeqGo;

    if ( ! include '/pad/sequence/build/one.php')
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>