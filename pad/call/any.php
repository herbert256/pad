<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return '';

  include pad . 'call/_call.php';
  
  if ( trim ( $padCallOB ) ) 

    if ( is_array ($padCallPHP) or $padCallPHP === TRUE or $padCallPHP === FALSE or $padCallPHP === NULL ) {

      // ToDo: What to do with $padCallOB in this case ?
      
    } else

      $padCallPHP .= $padCallOB;
 
  return $padCallPHP;

?>