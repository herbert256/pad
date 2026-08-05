<?php

  // Fires from pad/call/_init.php whenever the engine is about to include a PHP file -
  // a page's .php, a _lib file, _inits/_exits, a tag, a callback, a provider, an option
  // handler - since everything the engine executes goes through pad/call/.
  //
  // Traces the path held in $padCall when $padInfoTraceCall is set.

  global $padInfoTrace, $padInfoTraceCall;

  if ( $padInfoTrace )
    if ( $padInfoTraceCall )
      padInfoTrace ( 'call', 'info', $padCall );

?>