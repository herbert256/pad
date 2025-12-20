<?php

  global $padInfoStatsInfo, $padInfoTrace;

  if ( $padInfoTrace and function_exists ( 'padInfoTrace') ) {
    padInfoTrace ( 'stats', 'system',   $padInfoStatsInfo ['user']     ?? '' );
    padInfoTrace ( 'stats', 'user',     $padInfoStatsInfo ['system']   ?? '' );
    padInfoTrace ( 'stats', 'duration', $padInfoStatsInfo ['duration'] ?? '' );
  }

?>