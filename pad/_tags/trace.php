<?php

  if ( $padWalk [$pad] == 'start') {

    $padWalk [$pad] = 'end';

    $padTraceSaveTrace [$pad] = $padTrace;
    $padTraceSaveStart [$pad] = $padTraceStart ?? 0;
    $padTraceSaveTypes [$pad] = $padTraceTypes;  

    include_once pad . 'trace/trace.php';

    $padTrace = $padTraceBase ?? 0;
    $padTrace++;

    $padTraceStart = $pad;

    foreach ( $padTraceTypes as $padK => $padV )
      $padTraceTypes [$padK] = TRUE;

    $padTraceId    [$pad] = $padTrace;
    $padTraceLevel [$pad] = $padTrace;

    padTrace     ( 'trace', 'start' );
    padDumpToDir ( '', $GLOBALS ['padTraceDir'] . "/START" );

    return TRUE;

  }

  padTrace     ( 'trace', 'end' );
  padDumpToDir ( '', $GLOBALS ['padTraceDir'] . "/END" );

  $padTraceBase = $padTrace;

  $padTrace      = $padTraceSaveTrace [$pad];
  $padTraceStart = $padTraceSaveStart [$pad];
  $padTraceTypes = $padTraceSaveTypes [$pad];

  return TRUE;
  
?>