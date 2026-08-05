<?php

  // Calls $padCall and returns only what the file echoed, discarding its return value. Used
  // where the PHP file is expected to print its result, such as app tags in types/_go/tag.php.

  include PAD . 'call/_call.php';

  return $padCallOB;

?>