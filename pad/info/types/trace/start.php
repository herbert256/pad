<?php

  include_once pad . 'info/types/trace/_lib.php';

  set_time_limit ( 900 );

  $padTraceId          = 0;
  $padTraceLevel       = [];
  $padTraceLevelChilds = [];
  $padTraceOccurChilds = [];
  $padTraceMaxLevel    = 0;
  $padTraceSkipLevel   = 0;
  $padTraceDir         = "trace/$padStartPage/$padLog";

  if ( $padTraceStartEnd )
    padTrace ( 'trace', 'start' );
        
?>