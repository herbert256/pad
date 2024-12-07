<?php

  $padCallPHP = $padCallOB = '';

  if ( ! file_exists ( $padCall ) )
    return '';

  include 'call/_once.php';

  return include 'call/return.php';

?>