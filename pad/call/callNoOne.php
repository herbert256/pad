<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return;

  include pad . 'call/_call.php';

  if ($padCallPHP === 1)
    $padCallPHP = '';

  return $padCallOB;

?>