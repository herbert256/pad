<?php

  // Returns the source of the app page named $padGetName, loaded by get/go/call.php.
  // Lets a page be pulled in as content without going through a request of its own.

  $padGetCall = APP2 . padAppPageCheck ( $padGetName );

  return include PAD . 'get/go/call.php';

?>