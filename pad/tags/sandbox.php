<?php

  // The {sandbox} tag: runs the tag's own content as PAD source in a nested engine pass -
  // byte for byte what tags/code.php does, both clearing $padContent and handing over to
  // start/code.php. The isolation itself comes from the sandbox option that
  // start/pad/parms.php reads off the tag, not from the tag name.

  $padContent = '';

  return include PAD . 'start/code.php';

?>