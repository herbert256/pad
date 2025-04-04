<?php

  $padRandomlyStart = $padSeqStart;
  $padRandomlyEnd   = $padSeqEnd;

  if ( $padSeqBuildType == 'fixed' ) {

    if ( $padSeqEnd == PHP_INT_MAX )
      $padRandomlyEnd = count ( $padSeqFixed ) - 1;

    $padRandomlyRand = rand ( $padRandomlyStart, $padRandomlyEnd ) ;
    
    return $padSeqFixed [$padRandomlyRand];

  }

  if ( $padSeqInc <> 1 ) {

    $padRandomlySteps = intval ( ( $padRandomlyEnd - $padRandomlyStart ) / $padSeqInc );

    $padRandomlyRand = rand ( 0, $padRandomlySteps ) ;

    return $padSeqLoop + ( $padRandomlyRand * $padSeqInc );

  }

  return rand ( $padRandomlyStart, $padRandomlyEnd ); 

?>