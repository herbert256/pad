<?php

  $padSeqRandomStart = $padSeqStart;
  $padSeqRandomEnd   = $padSeqEnd;

  if ( $padSeqRandomStart == 1 and $padSeqMin <> PHP_INT_MIN ) 
    $padSeqRandomStart = $padSeqMin;

  if ( $padSeqRandomEnd == PHP_INT_MAX and $padSeqMax <> PHP_INT_MAX ) 
    $padSeqRandomEnd = $padSeqMax;

?>