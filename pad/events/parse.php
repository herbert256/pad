<?php

  // Fires from level/level.php for every { } pair the level loop finds, once padLevelBetween
  // has set $padBetween, $padStart [$pad] and $padEnd [$pad] - before it is told apart as a
  // tag, a closing tag, a comment or a variable. Traces it as found in the output when
  // $padInfoTraceParse is on: the raw '{...}' text, up to 50 characters of the output
  // preceding it, and what follows it (cut, as every trace info, to padMakeSafe's 2048).

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
