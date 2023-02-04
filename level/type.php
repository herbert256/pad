<?php

  $padTypeGiven = FALSE;

  $padTypeExplode = padExplode ($padTypeCheck, ':') ;

  if ( count ($padTypeExplode) == 0 or count ($padTypeExplode) > 2 )
    
    $padTypeResult = FALSE;

  elseif ( count ($padTypeExplode) == 2 and $padTypeExplode [0] == 'int' ) {

    $padTypeGiven  = TRUE;
    $padTypeResult = 'internal';
    $padTypeCheck  = $padTypeExplode [1];
  
  } elseif ( count ($padTypeExplode) == 1 ) {

    $padTypeResult = padTypeGet( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;
    $padTypeResult = $padTypeExplode [0];
    $padTypeCheck  = $padTypeExplode [1];

    $padTypeResult = padTypeCheck ( $padTypeResult, $padTypeCheck ); 

  }

  return $padTypeResult;

?>