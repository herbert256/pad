<?php

  global $pad, $padTraceLocalChk, $padTailDir, $padTraceSkipLevel, $padTraceMaxLevel, $padTraceLevel;

  if ( $GLOBALS ['padTraceSession'] )
    foreach ( padSessionEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  if ( $GLOBALS ['padTraceStartEnd'] )
     padTrace ( 'trace', 'end' );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( "$padTailDir/trace" );

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;
  
  $padTraceLevel [$pad] = '';

?>