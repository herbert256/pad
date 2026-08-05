<?php

  // Unreferenced: nothing in the engine includes this file. It runs the tag's content as
  // PAD source with the sandbox, clean and reset flags forced on and the function flag off,
  // which is what {sandbox} would need - but tags/sandbox.php goes to start/code.php like
  // {code} does, and takes those flags from the tag's own options via start/pad/parms.php.

  $padStrBld  = 'code';
  $padStrCod  = $padBase [$pad];

  $padStrBox = 1;
  $padStrCln = 1;
  $padStrRes = 1;
  $padStrFun = 0;

  return include PAD . 'start/pad/pad.php';

?>