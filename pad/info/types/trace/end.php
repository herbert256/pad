<?php

  if ( ! function_exists ( 'padTrace') )
    return;

  global $pad, $padTraceLocalChk, $padTraceDir, $padTraceSkipLevel, $padTraceMaxLevel, $padTraceLevel;

  if ( $GLOBALS ['padTraceStartEnd'] )
     padTrace ( 'trace', 'end' );

  if ( $padTraceLocalChk )
    padTraceCheckLocal ( $padTraceDir );

  padDumpToDir ( '', "$padTraceDir/dump" );    

?>