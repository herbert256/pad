<?php

  if ( ! function_exists ( 'padInfoTrace') )
    return;

  global $pad, $padInfoTraceLocalChk, $padInfoTraceDir, $padInfoTraceSkipLevel, $padInfoTraceMaxLevel, $padInfoTraceLevel;

  if ( $GLOBALS ['padInfoTraceStartEnd'] )
    padInfoTrace ( 'trace', 'end' );

  if ( $padInfoTraceLocalChk )
    padInfoTraceCheckLocal ( $padInfoTraceDir );

  padDumpToDir ( '', "$padInfoTraceDir/dump" );    

?>