<?php

  include pad . 'trace/config/config.php';

  $padTraceBase = "trace/$padPage/$padReqID"; 

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  padTrace ( 'trace', 'start' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>