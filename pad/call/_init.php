<?php

  // Opening half of a call: clears $padCallPHP and $padCallOB, fires the 'call' event and
  // starts the output buffer that will capture whatever the included file echoes.

  global $padInfo;

  $padCallPHP = $padCallOB = '';

  if ( $padInfo )
    include PAD . 'events/call.php';

  ob_start();

 ?>