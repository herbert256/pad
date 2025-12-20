<?php

  global $padInfo;

  $padCallPHP = $padCallOB = '';

  if ( $padInfo )
    include PAD . 'events/call.php';

  ob_start();

 ?>