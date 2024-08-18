<?php

  $padSeqInc = 2;

  $padSeqStart = $padSeqFrom * 2;

  if ( $padSeqTo <> PHP_INT_MAX )
    $padSeqEnd = $padSeqTo * 2;

  if ( $padSeqStart % 2 )
    $padSeqStart++;

?>