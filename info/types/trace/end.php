<?php

  if ( ! function_exists ( 'padInfoTrace') )
    return;

  global $pad, $padInfoTraceLocalChk, $padInfoTraceDir, $padInfoTraceSkipLevel, $padInfoTraceMaxLevel, $padInfoTraceLevel, $padInfoTraceStartEnd, $padInfoTraceDump, $padInfoTraceCnt;

  if ( $padInfoTraceStartEnd )
    padInfoTrace ( 'trace', 'end', $padPad [$pad] );

  if ( $padInfoTraceLocalChk )
    padInfoTraceCheckLocal ( '' );

  if ( $padInfoTraceDump )
    padDumpToDir ( '', "$padInfoTraceDir/dump" ); 

  $padInfoTraceCnt--; 

?>