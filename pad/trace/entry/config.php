<?php

  set_time_limit ( 900 );

  include pad . 'trace/store/start.php';

  $padTraceType = 'config'; 
  $padTraceGo   = 0;
 
  include pad . 'trace/trace/start.php';

  if ( $padTraceStartEnd and $padTraceType <> 'tag' )
    padTrace ( 'trace', 'start' );

  if ( $padTraceRequest )
    padTrackFileCallInit ( "$padTraceBase/request.json" );

  if ( $padTraceDumps ) 
    padTraceDump ( 'dump-start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>