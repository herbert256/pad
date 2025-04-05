<?php

  if ( ! $padSeqRandomly )
    return;
  
  $padSeqRandomlyStart = $padSeqStart;
  $padSeqRandomlyEnd   = $padSeqEnd;

  if ( $padSeqRandomlyStart == 1 and $padSeqMin <> PHP_INT_MIN ) 
    $padSeqRandomlyStart = $padSeqMin;

  if ( $padSeqRandomlyEnd == PHP_INT_MAX and $padSeqMax <> PHP_INT_MAX ) 
    $padSeqRandomlyEnd = $padSeqMax;

  if ( $padSeqBuildType == 'fixed' ) {

    if ( $padSeqRandomlyEnd > count ( $padSeqFixed ) - 1 )
      $padSeqRandomlyEnd = count ( $padSeqFixed ) - 1;
    
  }

  if ( $padSeqInc <> 1 )
    $padSeqRandomlySteps = intval ( ( $padSeqRandomlyEnd - $padSeqRandomlyStart ) / $padSeqInc );
  
?>