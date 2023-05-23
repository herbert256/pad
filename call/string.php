<?php

  include 'call.php';
  
  if ( is_array($padCallPHP) )

    $padCallPHP = padMakeContent ( $padCallPHP );
  
  elseif ( $padCallPHP === TRUE or $padCallPHP === FALSE or $padCallPHP === NULL )
  
    $padCallPHP = '';

  return $padCallPHP . $padCallOB;

?>