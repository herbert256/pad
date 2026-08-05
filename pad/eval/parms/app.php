<?php

  // Calls a pipe function that the application supplies in its own _functions/ directory.
  //
  // Reached from eval/type/parms.php for kind 'app'. padAppFunctionCheck() walks the current
  // directory up to the app root to find _functions/$name, and call/any.php runs the file with
  // $value and $parm in scope, returning its return value plus anything it echoed.

  $padCall = APP2 . padAppFunctionCheck ( $name ) . '.php';

  return include PAD . 'call/any.php';

?>