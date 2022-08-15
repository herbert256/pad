<?php

  if ( ! file_exists($padCall) )
    return '';

  ob_start();
  
  padTimingStart ('app');
  $padCall_return = include $padCall;
  padTimingEnd ('app');

  if ( is_array($padCall_return) or is_object($padCall_return) or is_resource($padCall_return) )
    $padCall_return = padMakeContent ( $padCall_return );
  elseif ($padCall_return === 1 or $padCall_return === TRUE or $padCall_return === FALSE or $padCall_return === NULL)
    $padCall_return = '';

  $padCall_return .= ob_get_clean();

  return $padCall_return ;

?>