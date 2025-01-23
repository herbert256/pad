<?php

  global $pad, $padOut;

  $padFunctionPad  = $pad;
  $padFunctionData = $padOut [$pad];

  $padFunctionReturn = include 'tryCatch/go/function.php';

  $pad           = $padFunctionPad;
  $padOut [$pad] = $padFunctionData;

  return $padFunctionReturn;

?>