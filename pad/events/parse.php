<?php

  // Fires from level/level.php for every { } pair the level loop finds, once padLevelBetween
  // has set $padBetween, $padStart [$pad] and $padEnd [$pad] - before it is told apart as a
  // tag, a closing tag, a comment or a variable. Traces it as found in the output when
  // $padInfoTraceParse is on: the raw '{...}' text, up to 50 characters of the output
  // preceding it, and what follows it.
  //
  // What follows is cut to the 2048 bytes a trace line keeps before it is handed on - whole
  // characters, mb_strcut - since padMakeSafe cleans the whole text it gets before it cuts:
  // the rest of the level's output, copied and cleaned once for every tag, made the flag
  // quadratic in the page's length - a page of 6000 fields took 13 s with it, 2.5 s without.
  // Where the text has runs of white space, which the cleaning makes one blank, the line
  // now holds less than 2048 of what follows: enough to see where the tag stands.

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
 if ( $padInfoTrace ) padInfoTrace ( 'parse', 'after',  mb_strcut ( $padOut [$pad], $padEnd [$pad] + 1, 2048, 'UTF-8' ) );

?>
