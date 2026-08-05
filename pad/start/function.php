<?php

  // Entry point for running PAD source from PHP rather than from a tag: padCode() and
  // padSandbox() in lib/execute.php include this file after setting $padStrCod and the
  // $padStrBld / $padStrBox / $padStrCln / $padStrRes flags.
  //
  // The nested pass in start/pad/function.php leaves $pad and this level's output moved on,
  // which would corrupt the level the PHP caller was running inside, so both are captured
  // beforehand and put back afterwards. The nested pass's result is returned to the include.

  global $pad, $padOut;

  $padFunctionPad  = $pad;
  $padFunctionData = $padOut [$pad];

  $padFunctionReturn = include PAD . 'start/pad/function.php';

  $pad           = $padFunctionPad;
  $padOut [$pad] = $padFunctionData;

  return $padFunctionReturn;

?>