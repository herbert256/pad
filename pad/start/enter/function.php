<?php

  global $pad, $padPad;

  $padFunctionPad  = $pad;
  $padFunctionData = $padPad [$pad];

  $padFunctionReturn = include PAD . 'catch/function.php';

  $pad           = $padFunctionPad;
  $padPad [$pad] = $padFunctionData;

  return $padFunctionReturn;

?>