<?php

  if ( ! file_exists($padCall) )
    return '';

  ob_start();
  
  padTimingStart ('app');
  $padCallReturn = include $padCall;
  padTimingEnd ('app');

  if ( is_array($padCallReturn) or is_object($padCallReturn) or is_resource($padCallReturn) )
    $padCallReturn = padMakeContent ( $padCallReturn );
  elseif ($padCallReturn === 1 or $padCallReturn === TRUE or $padCallReturn === FALSE or $padCallReturn === NULL)
    $padCallReturn = '';

  $padCallReturn .= ob_get_clean();

  return $padCallReturn ;

?>