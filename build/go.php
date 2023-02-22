<?php

  if ( $padParse )
    return '';

  $padCallReturn = include 'one.php';
  
  if ( is_array($padCallReturn) or is_object($padCallReturn) or is_resource($padCallReturn) )
    $padCallReturn = padMakeContent ( $padCallReturn );
  elseif ($padCallReturn === TRUE or $padCallReturn === FALSE or $padCallReturn === NULL)
    $padCallReturn = '';

  return $padCallReturn ;

?>