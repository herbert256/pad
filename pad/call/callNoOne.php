<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return;

  include PAD . 'call/_call.php';

  if ($padCallPHP === 1)
    $padCallPHP = '';

  return $padCallOB;

?>