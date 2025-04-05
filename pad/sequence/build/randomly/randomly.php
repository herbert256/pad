<?php

  if ( $padSeqInc <> 1 )
    $padSeqRandomlyRand = $padSeqRandomlyStart + rand ( 0, $padSeqRandomlySteps ) * $padSeqInc;
  else
    $padSeqRandomlyRand = rand ( $padSeqRandomlyStart, $padSeqRandomlyEnd ) ;

  if ( $padSeqBuildType == 'fixed' )
    return $padSeqFixed [$padSeqRandomlyRand];
  else
    return $padSeqRandomlyRand;

?>