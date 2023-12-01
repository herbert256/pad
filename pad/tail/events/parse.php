<?php

  if ( ! $padTraceParse )
    return;

  if (  $padStart [$pad] > 50 ) {
    $padTraceParseStart = $padStart [$pad] - 50;
    $padTraceParseEnd   = 50;
  } else {
    $padTraceParseStart = 0;
    $padTraceParseEnd   = $padStart [$pad];
  }

  padTrace ( 'parse', 'start',  '{' . $padBetween . '}');
  padTrace ( 'parse', 'before', substr ( $padPad [$pad], $padTraceParseStart, $padTraceParseEnd ) ); 
  padTrace ( 'parse', 'after',  substr ( $padPad [$pad], $padEnd [$pad] + 1 ) ); 

?>