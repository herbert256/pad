<?php

  if ( $padSeqInc <> 1 ) {
  
    $padSeqRandomRand = rand ( 0, $padSeqRandomSteps ) * $padSeqInc ;

    return  $padSeqRandomStart + $padSeqRandomRand;

  }

  return rand ( $padSeqRandomStart, $padSeqRandomEnd );

?>