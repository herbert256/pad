<?php
  
  include_once pad . 'trace/lib/trace.php';

  $padTrace        = $padTraceBase ?? 0;
  $padTraceDirBase = "trace/$padPage/$padReqID"; 
  $padTraceDir     = $padTraceDirBase;

  if ( $padTraceTypes ['dumps'] )
    padDumpToDir ( '', $padTraceDir . "/start" );

  $padTraceId [$pad] = $padTrace + 1;

  $padTraceActive = TRUE;

  padTrace ( 'trace', 'start' );

?>