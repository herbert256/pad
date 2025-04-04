<?php

  if ( $padSeqInc <> 1 ) {
  
    $padSeqRandRand = rand ( 0, $padSeqRandSteps ) * $padSeqInc ;

    return  $padSeqRandStart + $padSeqRandRand;

  }

  return rand ( $padSeqRandStart, $padSeqRandEnd );

?>