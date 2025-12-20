<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
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

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parse', 'start',  '{' . $padBetween . '}');
 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parse', 'before', substr ( $padOut [$pad], $padInfoTraceParseStart, $padInfoTraceParseEnd ) );
 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'parse', 'after',  substr ( $padOut [$pad], $padEnd [$pad] + 1 ) );

?>
