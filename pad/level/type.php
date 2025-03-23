<?php

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;
  $padTypeSeq     = '';
  $padTypeReverse = '';
  $padTypeSeqTag  = '';

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypePrefix = '';
    $padTypeCheck  = $padTagCheck;
    $padTypeResult = padTypeGet ( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;
    $padTypePrefix = $padTypeExplode [0];  
    $padTypeCheck  = $padTypeExplode [1];  
    $padTypeResult = padTypeCheck ( $padTypePrefix, $padTypeCheck ); 

    if ( ! $padTypeResult )
      $padTypeResult = padTypeSeq ( $padTypePrefix, $padTypeCheck );     
  
  } 

?>