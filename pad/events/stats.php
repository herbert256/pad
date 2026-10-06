<?php

  // Fires from info/types/stats/end.php at the end of a request, once the timings have been
  // worked out into $padInfoStatsInfo, and copies them into the trace report as 'stats' lines.
  //
  // It read user, system and duration - keys stats/end.php never stores, under labels it
  // had swapped - so the lines came out empty. It reads the four that are stored.

  global $padInfoStatsInfo, $padInfoTrace;

  if ( $padInfoTrace and function_exists ( 'padInfoTrace') )
    foreach ( [ 'total', 'boot', 'usr', 'call' ] as $padK )
      padInfoTrace ( 'stats', $padK, $padInfoStatsInfo [$padK] ?? '' );

?>
