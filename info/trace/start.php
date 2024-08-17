<?php

  include_once '/pad/info/trace/_lib.php';

  set_time_limit ( 900 );

  $padInfoTraceId          = 0;
  $padInfoTraceLevel       = [];
  $padInfoTraceLevelChilds = [];
  $padInfoTraceOccurChilds = [];
  $padInfoTraceMaxLevel    = 0;
  $padInfoTraceSkipLevel   = 0;
  $padInfoTraceId          = hrtime ( true );
  $padInfoTraceDir         = "trace/$padLog-$padInfoTraceId";

  if ( $padInfoTraceStartEnd )
   padInfoTrace ( 'trace', 'start' );
        
?>