<?php

  // Console writer, used by the CLI app: prints the trimmed page plus a newline to stdout
  // and exits. $padSent guards against a second write should the exit path be reached
  // twice.

  if ( ( $padOutput ?? '' ) === '' or isset ( $padSent ) )
    return;

  echo trim($padOutput) . "\n";

  $padSent = TRUE;

  padExit ();

?>