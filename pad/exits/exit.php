<?php

  // The actual end of the request: marks the boot and error shutdown handlers as done so
  // they stay quiet, then exits.
  //
  // Reached from padExit() and from the error handlers. Everything that still had to be
  // written - headers, body, info pages - has already been written by then. Included
  // rather than called, so it exits from whatever scope it lands in.
  //
  // A command-line run reports how the request ended in its process status, the way a web
  // run reports it in the HTTP status: $stop is padExit's argument when the end came
  // through padExit, and absent on the error paths that include this file directly - all
  // of which are failures, so absent means failed. The SAPI decides, not $padOutputType:
  // a boot error ends the request before any configuration has said what the output is.

  global $padBootShutdown, $padSkipShutdown;

  $padBootShutdown = TRUE;
  $padSkipShutdown = TRUE;

  if ( PHP_SAPI == 'cli' )
    exit ( in_array ( substr ( (string) ( $stop ?? 500 ), 0, 1 ), [ '2', '3' ] ) ? 0 : 1 );

  exit;

?>