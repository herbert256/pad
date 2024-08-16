<?php

  global $pad, $padPad;

  $padFunctionPad  = $pad;
  $padFunctionData = $padPad [$pad];

  $padFunctionReturn = include pad . 'catch/function.php';

  $pad           = $padFunctionPad;
  $padPad [$pad] = $padFunctionData;

  return $padFunctionReturn;

?>