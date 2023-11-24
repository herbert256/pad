<?php

  global $pad, $padTraceLocalChk, $padTailDir, $padTraceSkipLevel, $padTraceMaxLevel, $padTraceLevel;

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( "$padTailDir/trace" );

  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;
  
  $padTraceLevel [$pad] = '';

?>