<?php
 
  if ( $GLOBALS ['padInfoTrace'] and function_exists ( 'padInfoTrace') ) {
    padInfoTrace ( 'stats', 'system',   $GLOBALS ['padInfoStatsInfo'] ['user']     ?? '' );
    padInfoTrace ( 'stats', 'user',     $GLOBALS ['padInfoStatsInfo'] ['system']   ?? '' );
    padInfoTrace ( 'stats', 'duration', $GLOBALS ['padInfoStatsInfo'] ['duration'] ?? '' );
  } 

?>