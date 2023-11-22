<?php

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  $padTraceBase = "trace/$padPage/" . hrtime(true); 

  include_once pad . 'tail/types/trace/lib/trace.php';
  include_once pad . 'tail/types/trace/lib/lib.php';

  include pad . "config/trace/$padTraceProfile.php";
  if ( padExists ( padApp . '_config/trace.php' ) )
    include padApp . '_config/trace.php' ;

  $padTraceActive      = TRUE;  
  $padTraceLevel       = [];
  $padTraceLevelChilds = [];
  $padTraceOccurChilds = [];
  $padTraceMaxLevel    = 0;
  $padTraceSkipLevel   = 0;
        
?>