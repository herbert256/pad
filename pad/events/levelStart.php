<?php

  // Fires from level/start.php once a level is fully set up - parameters parsed, data, base
  // and options resolved - and immediately before its occurrences are iterated.
  //
  // Writes the trace report's level block. The xref record used to be made here too, but a
  // tag that jumps or ends the request never gets this far, so it moved to events/tag.php,
  // which fires before the handler runs.

  global $padInfoTrace;

  if ( $padInfoTrace )
    include PAD . 'info/types/trace/level/info.php';

?>