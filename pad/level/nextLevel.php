<?php

  // Carries out a jump to an outer level requested by a tag handler through
  // $padNextPadLevel - how {continue} and {break} unwind. $pad is moved to that level and
  // its remaining template text - everything from the tag being processed on - is thrown
  // away, so the current occurrence ends immediately, and the request is cleared.
  //
  // Only the remainder goes: $padOut is the whole working copy of the occurrence, with what
  // already rendered resolved in place before $padStart. Clearing all of it, as this used
  // to, threw the rendered part away too - a row that printed before its {break} lost that
  // print, where "like PHP's break" keeps it.

  $pad = $padNextPadLevel;

  $padOut [$pad] = substr ( $padOut [$pad], 0, $padStart [$pad] );

  $padNextPadLevel = 0;

?>