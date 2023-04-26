<?php

  if ( ! padExists($padCall) )
    return '';

  ob_start();
  
  $padCallReturn = include $padCall;

  if ( $padCallReturn === 1 )
    $padCallReturn = '';

  $padCallReturn .= ob_get_clean();

  return $padCallReturn ;

?>