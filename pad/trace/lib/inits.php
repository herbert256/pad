<?php
  
  include_once pad . 'trace/lib/trace.php';
  include      pad . 'trace/config/config.php';

  $padTrace        = $padTraceBase ?? 0;
  
  $padTraceDirBase = padFileCorrect ( "trace/$padPage/$padReqID" ); 

  $padTraceDir     = $padTraceDirBase;

  if ( $padTraceItems ['dumps'] )
    padDumpToDir ( '', $padTraceDir . "/start" );

  $padTraceId [$pad] = $padTrace + 1;

  $padTraceActive = TRUE;

  padTrace ( 'trace', 'start' );

  if ( $padTraceItems ['session'] )
    foreach ( padTrackFileRequestStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>