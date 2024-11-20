<?php

  global $pad, $padOut;

  $padFunctionPad  = $pad;
  $padFunctionData = $padOut [$pad];

  $padFunctionReturn = include 'catch/function.php';

  $pad           = $padFunctionPad;
  $padOut [$pad] = $padFunctionData;

  return $padFunctionReturn;

?>