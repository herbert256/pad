<?php

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  $padTraceBase = "trace/$padPage/" . hrtime(true); 

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';

  include pad . "config/trace/$padTraceProfile.php";

  if ( padExists ( padApp . '_config/trace.php' ) )
    include padApp . '_config/trace.php' ;
        
  if ( $padTraceRequest )
    padTrackFileCall ( "$padTraceBase/request.json" );

  $padTraceActive    = TRUE;  
  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  $padTraceLevel = [];

  $padTraceLevelChilds = [];
  $padTraceOccurChilds = [];

  if ( $padTraceStartEnd )
    padTrace ( 'trace', 'start' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'dump-start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>