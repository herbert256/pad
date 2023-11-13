<?php

  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  if ( $padTraceDumps ) 
    padTraceDump ( 'dump-end' );

  if ( $padTraceStartEnd )
     padTrace ( 'trace', 'end' );

  include pad . 'trace/trace/end.php';
  include pad . 'trace/store/end.php';
  
  return TRUE;

?>