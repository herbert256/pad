<?php

  $padTypeCheck   = $padWords [0];
  $padTypeResult  = FALSE;
  $padTypeGiven   = FALSE;
  $padTypeExplode = padExplode ($padTypeCheck, ':') ;

  if ( count ($padTypeExplode) == 1 )

    $padTypeResult = padTypeGet( $padTypeCheck );

  elseif ( count ($padTypeExplode) == 2 ) {  // ToDo: moet nog getest worden

    $padTypeGiven  = TRUE;
    $padTypeCheck  = $padTypeExplode [1];       
    $padTypeResult = padTypeCheck ( $padTypeExplode [0], $padTypeCheck ); 

  } 

  if ( $padTypeResult === FALSE ) 
    padIgnore ('type');

  return $padTypeResult;

?>