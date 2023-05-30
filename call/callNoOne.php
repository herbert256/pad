<?php

  $padCallPHP = $padCallOB = '';

  if ( ! padExists ( $padCall ) )
    return;

  include pad . 'call/_call.php';

  if ($padCallPHP === 1)
    $padCallPHP = '';

?>