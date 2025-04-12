<?php

  if ( $padSeqRandomStep <> 1 ) {
  
    $padSeqRandomRand = rand ( 0, $padSeqRandomSteps ) * $padSeqRandomStep;

    return  $padSeqRandomStart + $padSeqRandomRand;

  }

  return rand ( $padSeqRandomStart, $padSeqRandomEnd );

?>