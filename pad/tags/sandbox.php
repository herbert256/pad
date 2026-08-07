<?php

  // The {sandbox} tag: runs the tag's own content as PAD source in a nested engine pass -
  // byte for byte what tags/code.php does, both handing over to start/code.php and clearing
  // $padContent after it, for the reason given there. The isolation itself comes from the
  // sandbox option that start/pad/parms.php reads off the tag, not from the tag name.

  $padCodeResult = include PAD . 'start/code.php';

  $padContent = '';

  return $padCodeResult;

?>