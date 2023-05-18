<?php

  if ( ! padExists($padCall) )
    return '';

  ob_start();
  
  if ( str_ends_with($padCall, '/_lib.php'))
    $padCallReturn = include_once $padCall;
  else
    $padCallReturn = include $padCall;

  if ( $padCallReturn === 1 )
    $padCallReturn = '';

  if ( is_array ($padCallReturn) )
    ob_get_clean();
  else
    $padCallReturn .= ob_get_clean();

  return $padCallReturn ;

?>