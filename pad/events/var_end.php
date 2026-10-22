<?php

  // Closing counterpart of events/var_start.php: fires from level/var.php when the value of
  // the variable tag is final - pipes and sanitize chains applied - and traces it when
  // $padInfoTraceVar is on, before padProtect makes its braces inert and padLevel splices
  // it into the output.

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceVar )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'var', 'end', 'value=' . $padVal );

?>
