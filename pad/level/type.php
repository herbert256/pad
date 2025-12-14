<?php

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;
  $padTypeSeq     = '';

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypePrefix = '';
    $padTypeCheck  = $padTagCheck;
    $padTypeResult = padTypeTag ( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;

    $padTypePrefix = $padTypeExplode [0];
    $padTypeCheck  = $padTypeExplode [1];

    $padTypeResult = padTypeTagCheck ( $padTypePrefix, $padTypeCheck );

    if ( ! $padTypeResult )
      $padTypeResult = padTypeSeq ( $padTypePrefix, $padTypeCheck );

  }

?>