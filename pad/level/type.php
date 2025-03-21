<?php

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypePrefix = '';
    $padTypeCheck  = $padTagCheck;
    $padTypeResult = padTypeGet( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;
    $padTypePrefix = $padTypeExplode [0];  
    $padTypeCheck  = $padTypeExplode [1];  

    if (     $padTypeCheck and isset ( $padSeqStore [$padTypeCheck] ) and file_exists ("seq/one/types/$padTypePrefix.php") )
      $padTypeResult = 'one';
    elseif ( $padTypeCheck and isset ( $padSeqStore [$padTypeCheck] ) and file_exists ("seq/actions/types/$padTypePrefix.php") )
      $padTypeResult = 'action';
    else
      $padTypeResult = padTypeCheck ( $padTypePrefix, $padTypeCheck ); 

  } 

  return $padTypeResult;

?>