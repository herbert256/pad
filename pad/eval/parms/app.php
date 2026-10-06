<?php

  // Calls a pipe function that the application supplies in its own _functions/ directory.
  //
  // Reached from eval/type/parms.php for kind 'app'. padAppFunctionCheck() walks the current
  // directory up to the app root to find _functions/$name, and call/any.php runs the file with
  // $value and $parm in scope, returning its return value plus anything it echoed.

  $padCall = APP2 . padAppFunctionCheck ( $name ) . '.php';

  // The piped value goes in as $padContent too - the name FUNCTIONS.md and CLAUDE.md give
  // it - next to the $value the existing function files read. This runs inside padEval,
  // so the engine's own $padContent is not touched.

  $padContent = $value;

  return include PAD . 'call/any.php';

?>
