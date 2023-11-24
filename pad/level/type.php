<?php

  $padTypeExplode = padExplode ($padWords [0], ':') ;

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypeCheck  = $padWords [0];
    $padTypeResult = padTypeGet( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;
    $padTypeCheck  = $padTypeExplode [1];       
    $padTypeResult = padTypeCheck ( $padTypeExplode [0], $padTypeCheck ); 

  } 

  return $padTypeResult;

?>