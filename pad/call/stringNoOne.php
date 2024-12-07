<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return '';

  include 'call/_call.php';

  return include 'call/return.php';

?>