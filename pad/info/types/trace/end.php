<?php

  if ( ! function_exists ( 'padTrace') )
    return;

  global $pad, $padTraceLocalChk, $padInfoDir, $padTraceSkipLevel, $padTraceMaxLevel, $padTraceLevel, $padTraceDir;

  if ( $GLOBALS ['padTraceSession'] )
    foreach ( padSessionEnd () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

  if ( $GLOBALS ['padTraceStartEnd'] )
     padTrace ( 'trace', 'end' );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( "$padInfoDir/trace" );

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;
  
  $padTraceLevel [$pad] = '';

?>