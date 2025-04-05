<?php

  if ( $padSeqInc <> 1 )
    $padSeqRandomlyRand = rand ( 0, $padSeqRandomlySteps ) * $padSeqInc;
  else
    $padSeqRandomlyRand = rand ( $padSeqRandomlyStart, $padSeqRandomlyEnd ) ;

  if ( $padSeqBuildType == 'fixed' )
    return $padSeqFixed [$padSeqRandomlyRand];
  else
    return $padSeqRandomlyRand;

?>