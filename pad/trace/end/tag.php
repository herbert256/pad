<?php

  padTrace ( 'trace', 'end' );
  
  padDumpToDir ( '', padTraceDir () . "/END" );

  $padTraceBase  = $padTrace;

  $padTrace      = $padTraceSaveTrace [$pad];
  $padTraceTypes = $padTraceSaveTypes [$pad];
  $padTraceTree  = $padTraceSaveTree  [$pad]
  $padTraceStart = $padTraceSaveStart [$pad];
  
  return TRUE;
  
?>