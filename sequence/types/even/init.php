<?php

  $padSeqInc = 2;

  $padSeqLoopStart = $padSeqFrom * 2;

  if ( $padSeqTo <> PHP_INT_MAX )
    $padSeqLoopEnd = $padSeqTo * 2;

  if ( $padSeqLoopStart % 2 )
    $padSeqLoopStart++;

?>