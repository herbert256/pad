<?php

  include_once pad . 'tail/types/trace/lib.php';

  set_time_limit ( 900 );

  if ( $padTraceStartEnd )
    padTrace ( 'trace', 'start' );

  if ( $padTraceSession )
    foreach ( padSessionStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );  

  $padTraceLevel       = [];
  $padTraceLevelChilds = [];
  $padTraceOccurChilds = [];
  $padTraceMaxLevel    = 0;
  $padTraceSkipLevel   = 0;
        
?>