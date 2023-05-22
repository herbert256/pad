<?php

  $padCallPHP = $padCallOB = '';

  if ( ! padExists ( $padCall ) )
    return;

  ob_start();

  $padCallPHP = include $padCall;
  $padCallOB  = ob_get_clean();

  if ( $padCallPHP === 1 )
    $padCallPHP = '';

?>