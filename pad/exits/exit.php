<?php

  // The actual end of the request: marks the boot and error shutdown handlers as done so
  // they stay quiet, then exits.
  //
  // Reached from padExit() and from the error handlers. Everything that still had to be
  // written - headers, body, info pages - has already been written by then. Included
  // rather than called, so it exits from whatever scope it lands in.

  global $padBootShutdown, $padSkipShutdown;

  $padBootShutdown = TRUE;
  $padSkipShutdown = TRUE;

  exit;

?>