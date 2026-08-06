<?php

  // Calls a pipe function named with the function: prefix, the handler for kind 'function'.
  //
  // The prefix only says "read this name as a function"; which kind of function it is still
  // has to be resolved, exactly as a bare name in a pipe is. padTypeFunction() answers that -
  // 'app' for one the application supplies in _functions/, 'pad' for a built-in - and the
  // handler for that kind does the call.
  //
  // The tag form of the same prefix is types/function.php. Only that half existed, so
  // {function:upper} worked as a tag while {echo $x | function:upper} - the spelling CLAUDE.md
  // documents - ended the request on a missing include.

  $padFunctionKind = padTypeFunction ( $name, 0 );

  if ( ! $padFunctionKind or ! file_exists ( PAD . "eval/parms/$padFunctionKind.php" ) )
    return padError ( "Function '$name' is not a function" );

  return include PAD . "eval/parms/$padFunctionKind.php";

?>
