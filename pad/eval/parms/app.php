<?php

  // Calls a pipe function that the application supplies in its own _functions/ directory.
  //
  // Reached from padEvalCall (lib/eval/run.php) for kind 'app'. padAppFunctionCheck() walks the
  // current directory up to the app root to find _functions/$name, and call/any.php runs the
  // file with $value and $parm in scope, returning its return value plus anything it echoed.

  // The app: prefix names the kind outright, and a name with no _functions/ file behind it
  // was handed to the call as APP2 . '.php', which runs nothing: {echo $x | app:nosuch}
  // answered empty where the tag form {app:nosuch} is an error. Named under the strict
  // check, empty in the lenient walk.

  $padCall = APP2 . padAppFunctionCheck ( $name ) . '.php';

  if ( ! padAppFunctionCheck ( $name ) or ! file_exists ( $padCall ) ) {

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "there is no application function named '$name'" );

    return '';

  }

  // The piped value goes in as $padContent too - the name FUNCTIONS.md and CLAUDE.md give
  // it - next to the $value the existing function files read. This runs inside padEval,
  // so the engine's own $padContent is not touched.

  $padContent = $value;

  // A date object it returns stays one (call/_exit.php), for the date, ago and localDate
  // pipes after it: made an array, {$d | due | date('Y')} ended on "Array to string
  // conversion" and {$d | due} printed nothing.

  $padCallDate = TRUE;

  return include PAD . 'call/any.php';

?>
