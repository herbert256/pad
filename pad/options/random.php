<?php

  $padRndRandom = ( $padPrm [$pad] ['random'] === TRUE) ? 1 : $padPrm [$pad] ['random'];

  $padRndTemp = [];

  for ($padRndI=1; $padRndI <= $padRndRandom; $padRndI++) { 

    if ( ! count($padData [$pad]) ) 
      break;

    $padRndRand = rand (1, count($padData [$pad]));

    $padRndNow = 0;
    foreach ( $padData [$pad] as $padRndKey => $padRndValue ) {
      $padRndNow++;
      if ( $padRndNow == $padRndRand ) {
        $padRndTemp [$padRndKey] = $padData [$pad] [$padRndKey] ;
        unset ( $padData [$pad] [$padRndKey] );
        break;
      }
    }
      
  }

  $padData [$pad] = $padRndTemp;

?>