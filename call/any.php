<?php

  include 'call.php';

  if ( ! padExists ( $padCall ) )
    return '';

  if ( trim ( $padCallOB ) ) {

    if ( is_array ($padCallPHP) or is_object ($padCallPHP) or is_resource ($padCallPHP) ) 
      return padError ( "If an array is returned, then console output is not allowed: $padCall" );

    if ( $padCallPHP === TRUE or $padCallPHP === FALSE or $padCallPHP === NULL ) 
      $padCallPHP = '';

    return $padCallPHP . $padCallOB;

  }
 
  return $padCallPHP;

?>