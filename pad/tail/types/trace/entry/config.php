<?php

  set_time_limit ( 900 );

  $padTraceType = 'config'; 
  $padTraceGo   = 0;
 
  include pad . 'tail/types/trace/trace/start.php';

  if ( $padTraceStartEnd and $padTraceType <> 'tag' )
    padTrace ( 'trace', 'start' );

  if ( $padTraceSession )
    foreach ( padSessionStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>