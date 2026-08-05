<?php

  // Closes a 'trace' scope opened by info/types/trace/start.php - from info/end/config.php at
  // the end of the request, or from pad/tags/trace.php at the closing {/trace}.
  //
  // Traces the final result, lets padInfoTraceCheckLocal delete the top-level files that turned
  // out to be copies of each other, optionally writes a complete dump beside the trace
  // ($padInfoTraceDump) and pops the scope off $padInfoTraceCnt.

  if ( ! function_exists ( 'padInfoTrace') )
    return;

  global $pad, $padInfoTraceLocalChk, $padInfoTraceDir, $padInfoTraceSkipLevel, $padInfoTraceMaxLevel, $padInfoTraceLevel, $padInfoTraceStartEnd, $padInfoTraceDump, $padInfoTraceCnt;

  if ( $padInfoTraceStartEnd )
    padInfoTrace ( 'trace', 'end', $padResult [$pad] ?? '');

  if ( $padInfoTraceLocalChk )
    padInfoTraceCheckLocal ( '' );

  if ( $padInfoTraceDump )
    padDumpToDir ( '', "$padInfoTraceDir/dump" );

  $padInfoTraceCnt--;

?>