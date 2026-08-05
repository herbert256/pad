<?php

  // Dormant hook - nothing in the engine includes events/cache.php, so it never runs.
  // The body duplicates events/call.php (trace $padCall when $padInfoTraceCall), and was
  // presumably meant to mark a call served from the cache rather than executed.

  global $padInfoTrace;

  if ( $padInfoTrace )
    if ( $padInfoTraceCall )
      padInfoTrace ( 'call', 'info', $padCall );

?>