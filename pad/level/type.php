<?php

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;
  $padTypeSeq     = '';

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypePrefix = '';
    $padTypeCheck  = $padTagCheck;
    $padTypeResult = padTypeGet ( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;
    $padTypePrefix = $padTypeExplode [0];  
    $padTypeCheck  = $padTypeExplode [1];  
    $padTypeResult = padTypeSeq ( $padTypePrefix, $padTypeCheck ); 

    if ( $padTypeResult ) 
      $padTypeSeq  = $padTypePrefix;
    else
      $padTypeResult = padTypeCheck ( $padTypePrefix, $padTypeCheck ); 

  } 

?>