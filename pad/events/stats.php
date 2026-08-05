<?php

  // Fires from info/types/stats/end.php at the end of a request, once the timings have been
  // worked out into $padInfoStatsInfo, and copies them into the trace report as 'stats' lines.
  //
  // The keys read here do not match the ones stats/end.php stores (total, boot, usr, call),
  // so the lines currently come out empty.

  global $padInfoStatsInfo, $padInfoTrace;

  if ( $padInfoTrace and function_exists ( 'padInfoTrace') ) {
    padInfoTrace ( 'stats', 'system',   $padInfoStatsInfo ['user']     ?? '' );
    padInfoTrace ( 'stats', 'user',     $padInfoStatsInfo ['system']   ?? '' );
    padInfoTrace ( 'stats', 'duration', $padInfoStatsInfo ['duration'] ?? '' );
  }

?>