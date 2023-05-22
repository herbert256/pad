<?php

  include 'call.php';
  
  if ( is_array($padCallPHP) or is_object($padCallPHP) or is_resource($padCallPHP) )
    $padCallPHP = padMakeContent ( $padCallPHP );
  elseif ( $padCallPHP === TRUE or $padCallPHP === FALSE or $padCallPHP === NULL )
    $padCallPHP = '';

  return $padCallPHP . $padCallOB;;

?>