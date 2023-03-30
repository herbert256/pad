<?php

  if ( $GLOBALS['padParse'] )
    return '';

  if ( ! padExists($padCall) )
    return '';

  ob_start();
  
  padTimingStart ('padApp');
  $padCallReturn = include $padCall;
  padTimingEnd ('padApp');

  if ( $padCallReturn === 1 )
    $padCallReturn = '';

  $padCallReturn .= ob_get_clean();

  return $padCallReturn ;

?>