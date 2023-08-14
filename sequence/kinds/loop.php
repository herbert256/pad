<?php

  $padSeqGo = $padSeqLoopStart;

  while ( $padSeqGo <= $padSeqLoopEnd ) {

    $padSeqLoop = $padSeqGo;

    if ( ! include pad . 'sequence/one.php')
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>