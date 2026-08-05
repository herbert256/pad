<?php

  // Fires from the trace report's occurrence-end handler (info/types/trace/occur/end.php, so
  // only when trace is on) and logs the output that one iteration produced, $padOut [$pad],
  // when $padInfoTraceResultOcc is set.
  //
  // Skipped when content tracing already showed the same text and the occurrence changed
  // nothing.

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceResultOcc )
    return;

  if ( !$padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padOut [$pad] )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'occur', 'occ-result', $padOut [$pad] );

?>