<?php

  $padSeqInit = TRUE;

  $padSeqGo = $padSeqLoopStart;

  while ( $padSeqGo <= $padSeqLoopEnd ) {

    if ( ! $padSeqRandom )

      $padSeqLoop = $padSeqGo;

    elseif ( $padSeqInc == 1)

      $padSeqLoop = padSeqRandom ( $padSeqLoopStart, $padSeqLoopEnd );

    else {

      $padSeqIncCnt = round ( (($padSeqLoopEnd-$padSeqLoopStart)+1) / $padSeqInc );
      $padSeqIncCnt = padSeqRandom ( 0, $padSeqIncCnt );

      $padSeqLoop = $padSeqLoopStart + ($padSeqIncCnt*$padSeqInc);

    }

    if ( ! include 'sequence/type/go/one.php')
      break;

    $padSeqInit = FALSE;

    $padSeqGo = $padSeqGo + $padSeqInc;

  }

?>