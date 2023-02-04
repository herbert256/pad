<?php

  $padTypeExplode = padExplode ($padTypeCheck, ':') ;

  if ( count ($padTypeExplode) == 0 or count ($padTypeExplode) > 2 )
    return FALSE;

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeResult = padTypeGet( $padTypeCheck );

    return ( $padTypeResult ) ? $padTypeResult : FALSE; 

  } else {

    $padTypeResult = $padTypeExplode [0];
    $padTypeCheck  = $padTypeExplode [1];

    return ( padTypeCheck ( $padTypeResult, $padTypeCheck ) ) ? $padTypeResult : FALSE; 

  }

?>