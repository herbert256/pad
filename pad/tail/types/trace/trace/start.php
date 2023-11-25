<?php

  include_once pad . 'tail/types/trace/lib/trace.php';
  include_once pad . 'tail/types/trace/lib/lib.php';

  $padTraceActive      = TRUE;  
  $padTraceLevel       = [];
  $padTraceLevelChilds = [];
  $padTraceOccurChilds = [];
  $padTraceMaxLevel    = 0;
  $padTraceSkipLevel   = 0;
        
?>