<?php

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceParse )
    return;

  if (  $padStart [$pad] > 50 ) {
    $padInfoTraceParseStart = $padStart [$pad] - 50;
    $padInfoTraceParseEnd   = 50;
  } else {
    $padInfoTraceParseStart = 0;
    $padInfoTraceParseEnd   = $padStart [$pad];
  }

 if ( $padInfoTrace ) padInfoTrace ( 'parse', 'start',  '{' . $padBetween . '}');
 if ( $padInfoTrace ) padInfoTrace ( 'parse', 'before', substr ( $padOut [$pad], $padInfoTraceParseStart, $padInfoTraceParseEnd ) );
 if ( $padInfoTrace ) padInfoTrace ( 'parse', 'after',  substr ( $padOut [$pad], $padEnd [$pad] + 1 ) );

?>
