<?php

  if ( ! file_exists($padCall) )
    return '';

  ob_start();
  
  pTiming_start ('app');
  $padCall_return = include $padCall;
  pTiming_end ('app');

  if ( is_array($padCall_return) or is_object($padCall_return) or is_resource($padCall_return) )
    $padCall_return = pMake_content ( $padCall_return );
  elseif ($padCall_return === 1 or $padCall_return === TRUE or $padCall_return === FALSE or $padCall_return === NULL)
    $padCall_return = '';

  $padCall_return .= ob_get_clean();

  return $padCall_return ;

?>