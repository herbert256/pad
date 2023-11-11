<?php

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  $padTraceBase = "trace/$padPage/$padReqID-" . padRandomString (8); 

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';

  include pad . 'trace/config/trace.php';

  if ( $padTraceRequest )
    padTrackFileCall ( "$padTraceBase/request.json" );

  $padTraceActive    = TRUE;  
  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  $padTraceLevel = [];

  $padTraceLevelChilds = [];
  $padTraceOccurChilds = [];

  padTrace ( 'trace', 'start' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'dump-start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>