<?php

  $padTraceActive = TRUE;

  include pad . 'trace/config/config.php';

  $padTraceBase = "trace/$padPage/$padReqID-" . padRandomString (8); 

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';
  include_once pad . 'trace/lib/set.php';

  if ( $padTraceXml )
    include_once pad . 'trace/xml/start.php';

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  $padTraceSpaces  = 0;

  padTraceSet ( 'trace', 'start' );

  if ( $padTraceOpenClose )
    padTrace ( 'trace', 'start' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>