<?php
 
  if ( $padTraceSession )
    foreach ( padSessionInfoEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  padTrace ( 'trace', 'end' );

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  padTraceCheckLocal ( $padTraceBase );

  $padTraceX1 = $padTraceLevel       [$pad] ?? '';
  $padTraceX2 = $padTraceLevelChilds [$pad] ?? 0;

  padTraceChilds ( $padTraceX1, $padTraceX2, 'level' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'end' );

  $padTraceActive = FALSE;

  return TRUE;

?>