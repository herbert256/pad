<?php

  $padSeqRandStart = $padSeqStart;
  $padSeqRandEnd   = $padSeqEnd;

  if ( $padSeqRandStart == 1 and $padSeqMin <> PHP_INT_MIN ) 
    $padSeqRandStart = $padSeqMin;

  if ( $padSeqRandEnd == PHP_INT_MAX and $padSeqMax <> PHP_INT_MAX ) 
    $padSeqRandEnd = $padSeqMax;


  if ( $padSeqInc <> 1 )
    $padSeqRandSteps = intval ( ( $padSeqRandEnd - $padSeqRandStart ) / $padSeqInc );

  
?>