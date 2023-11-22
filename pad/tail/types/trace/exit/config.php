<?php

  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  if ( $padTraceDumps ) 
    padTraceDump ( 'dump-end' );

  if ( $padTraceStartEnd )
     padTrace ( 'trace', 'end' );

  include pad . 'tail/types/trace/trace/end.php';
  include pad . 'tail/types/trace/store/end.php';
  
  return TRUE;

?>