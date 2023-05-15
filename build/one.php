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

  $padCallReturn .= ob_get_clean();

  return $padCallReturn ;

?>