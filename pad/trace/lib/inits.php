<?php
  
  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  include_once pad . 'trace/lib/trace.php';
  include      pad . 'trace/config/config.php';
  
  $padTraceDirBase = padFileCorrect ( "trace/$padPage/$padReqID" ); 

  $padTraceDir     = $padTraceDirBase;

  if ( $padTraceDumps )
    padDumpToDir ( '', $padTraceDir . "/start" );

  $padTraceId [$pad] = $padTrace + 1;

  $padTraceActive = TRUE;

  padTrace ( 'trace', 'start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>