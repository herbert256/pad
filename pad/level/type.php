<?php

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;

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

  } 

?>