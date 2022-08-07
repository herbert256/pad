<?php

  if ( ! file_exists($pCall) )
    return '';

  ob_start();
  
  pTiming_start ('app');
  $pCall_return = include $pCall;
  pTiming_end ('app');

  if ( is_array($pCall_return) or is_object($pCall_return) or is_resource($pCall_return) )
    $pCall_return = pMake_content ( $pCall_return );
  elseif ($pCall_return === 1 or $pCall_return === TRUE or $pCall_return === FALSE or $pCall_return === NULL)
    $pCall_return = '';

  $pCall_return .= ob_get_clean();

  return $pCall_return ;

?>