<?php

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 1;

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';

  include pad . 'trace/config/trace.php';

  $padTraceActive    = TRUE;  
  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  $padTraceBase = "trace/$padPage/$padReqID-" . padRandomString (8); 

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