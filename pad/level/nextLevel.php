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

  // The levels between are abandoned, and each is closed as it would have closed itself:
  // its occurrence and level variables are undone - a {break 'outer'} from an inner loop
  // left the inner row's fields standing as globals, so {$name} after the loops read the
  // last row - and what it had rendered is kept, carried out to the level jumped to, the
  // way PHP keeps what a loop echoed before a break 2.

  $padNextCarry = '';

  for ( $padNextLevel = $pad; $padNextLevel > $padNextPadLevel; $padNextLevel-- ) {

    $padNextCarry = $padResult [$padNextLevel]
                  . substr ( $padOut [$padNextLevel], 0, $padStart [$padNextLevel] )
                  . $padNextCarry;

    $pad = $padNextLevel;

    padResetOcc ();
    padResetLvl ();

  }

  $pad = $padNextPadLevel;

  $padOut [$pad] = substr ( $padOut [$pad], 0, $padStart [$pad] ) . $padNextCarry;

  $padNextPadLevel = 0;

?>