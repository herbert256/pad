<?php

  // The {sandbox} tag: runs the tag's own content as PAD source in a nested engine pass -
  // byte for byte what tags/code.php does, both handing over to start/code.php and clearing
  // $padContent after it, for the reason given there. The isolation is start/pad/parms.php's:
  // it reads the sandbox option off a {code}, and takes this tag - and the sandbox pipe,
  // this tag run as a function - as sandboxed by its name.

  $padCodeResult = include PAD . 'start/code.php';

  $padContent = '';

  return $padCodeResult;

?>
