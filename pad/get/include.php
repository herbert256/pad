<?php

  // Returns the source of the _include/ snippet named $padGetName, found by searching the
  // directory chain, loaded by get/go/call.php. Backs the include: type.

  $padGetCall = APP2 . padAppIncludeCheck ( $padGetName );

  return include PAD . 'get/go/call.php';

?>