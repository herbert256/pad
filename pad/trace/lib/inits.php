<?php
  
  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';
  include      pad . 'trace/config/config.php';
  
  $padTraceBase = "trace/$padPage/$padReqID"; 

  if ( $padTraceDumps )
    padDumpToDir ( '', $padTraceBase . "/start" );

  $padTraceId [$pad] = $padTrace + 1;

  $padTraceActive = TRUE;

  padTrace ( 'trace', 'start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>