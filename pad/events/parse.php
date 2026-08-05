<?php

  // Traces a tag as it is found in the output: the raw '{...}' text, up to 50 characters of
  // the output preceding it, and everything that follows it - active when $padInfoTraceParse
  // is on.
  //
  // Nothing includes this file at present; it belongs where level/level.php locates the tag
  // delimiters and sets $padBetween, $padStart [$pad] and $padEnd [$pad].

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