<?php

  if ( ! function_exists ( 'padTrace') )
    return;

  global $pad, $padInfTraceLocalChk, $padInfTraceDir, $padInfTraceSkipLevel, $padInfTraceMaxLevel, $padInfTraceLevel;

  if ( $GLOBALS ['padTraceStartEnd'] )
    $GLOBALS ['padInfo']( 'trace', 'end' );

  if ( $padInfTraceLocalChk )
    padTraceCheckLocal ( $padInfTraceDir );

  padDumpToDir ( '', "$padInfTraceDir/dump" );    

?>