<?php

  if ( ! $padInfTraceParse )
    return;

  if (  $padStart [$pad] > 50 ) {
    $padInfTraceParseStart = $padStart [$pad] - 50;
    $padInfTraceParseEnd   = 50;
  } else {
    $padInfTraceParseStart = 0;
    $padInfTraceParseEnd   = $padStart [$pad];
  }

 padTrace ( 'parse', 'start',  '{' . $padBetween . '}');
 padTrace ( 'parse', 'before', substr ( $padPad [$pad], $padInfTraceParseStart, $padInfTraceParseEnd ) ); 
 padTrace ( 'parse', 'after',  substr ( $padPad [$pad], $padEnd [$pad] + 1 ) ); 

?>