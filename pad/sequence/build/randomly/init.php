<?php

  if ( ! $padSeqRandomly )
    return;
  
  $padSeqRandomlyStart = $padSeqStart;
  $padSeqRandomlyEnd   = $padSeqEnd;

  if ( $padSeqBuildType == 'fixed' )
    if ( $padSeqRandomlyEnd > count ( $padSeqFixed ) - 1 )
      $padSeqRandomlyEnd = count ( $padSeqFixed ) - 1;

  if ( $padSeqInc <> 1 )
    $padSeqRandomlySteps = intval ( ( $padSeqRandomlyEnd - $padSeqRandomlyStart ) / $padSeqInc );
  
?>