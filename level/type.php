<?php

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypeCheck  = $padTagCheck;
    $padTypeResult = padTypeGet( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;
    $padTypeCheck  = $padTypeExplode [1];       
    $padTypeResult = padTypeCheck ( $padTypeExplode [0], $padTypeCheck ); 

  } 

  return $padTypeResult;

?>