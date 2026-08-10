<?php

  // Catch handler for call/_try.php, the include of an application PHP file: reports the
  // throwable with its file and line through padErrorGo() and returns '' so the caller
  // ends up with an empty result instead of a fatal.

  padErrorGo (
    'CATCH: ' .
    $padTryException->getMessage(),
    $padTryException->getFile(),
    $padTryException->getLine()
  );

  $padCallPHP = '';
  $padCallOB  = '';

  return '';

?>