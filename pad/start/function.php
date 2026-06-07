<?php

  global $pad, $padOut;

  $padFunctionPad  = $pad;
  $padFunctionData = $padOut [$pad];

  $padFunctionReturn = include PAD . 'start/pad/function.php';

  $pad           = $padFunctionPad;
  $padOut [$pad] = $padFunctionData;

  return $padFunctionReturn;

?>