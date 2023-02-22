<?php

  if ( $padParse )
    return '';

  if ( ! file_exists($padCall) )
    return '';

  ob_start();
  
  padTimingStart ('app');
  $padCallReturn = include $padCall;
  padTimingEnd ('app');

  if ( $padCallReturn === 1 )
    $padCallReturn = '';

  $padCallReturn .= ob_get_clean();

  return $padCallReturn ;

?>