<?php

  $padSeqGo = $padSeqLoopStart;

  while ( $padSeqGo <= $padSeqLoopEnd ) {

    $padSeqLoop = $padSeqGo;

    if ( ! include pad . 'sequence/type/go/one.php')
      break;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>