<?php

  include_once '/pad/info/types/trace/_lib.php';

  set_time_limit ( 900 );

  $padInfTraceId          = 0;
  $padInfTraceLevel       = [];
  $padInfTraceLevelChilds = [];
  $padInfTraceOccurChilds = [];
  $padInfTraceMaxLevel    = 0;
  $padInfTraceSkipLevel   = 0;
  $padInfTraceDir         = "trace/$padStartPage/$padLog";

  if ( $padInfTraceStartEnd )
   $GLOBALS ['padInfo']( 'trace', 'start' );
        
?>