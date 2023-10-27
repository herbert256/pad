<?php

  $padTraceSaveTrace [$pad] = $padTrace;
  $padTraceSaveTypes [$pad] = $padTraceTypes;  
  $padTraceSaveTree  [$pad] = $padTraceTree  ?? FALSE;
  $padTraceSaveStart [$pad] = $padTraceStart ?? 0;

  $padTraceTree = TRUE;
  include pad . 'trace/lib/inits.php';

  $padTrace = $padTraceBase ?? 0;

  $padTraceId    [$pad] = $padTrace + 1;
  $padTraceLevel [$pad] = $padTrace + 1;

  $padTraceTree  = TRUE;
  $padTraceStart = $pad;

  padTrace ( 'trace', 'start' );

  padDumpToDir ( '', padTraceDir () . "/START" );

?>