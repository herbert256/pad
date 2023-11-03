<?php

  include pad . 'trace/config/config.php';

  $padTraceBase = "trace/$padPage/$padReqID"; 

  if ( $padTraceDumps )
    padDumpToDir ( '', $padTraceBase . "/start" );
  
  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';

  $padTraceLevelChilds [$pad] = 0;
  $padTraceOccurChilds [$pad] = [];

  padTrace ( 'trace', 'start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>