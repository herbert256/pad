<?php
 
  if ( ! function_exists ( 'padTrace') )
    return;

 padTrace ( 'stats', 'duration', padDuration () );
 padTrace ( 'stats', 'system',   $GLOBALS ['padStatsSystem'] );
 padTrace ( 'stats', 'user',     $GLOBALS ['padStatsUser'] );

?>