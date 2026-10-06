<?php

  // Build strategy 'loop' for the modulo sequence: each term is the loop value modulo the
  // parameter, which defaults to 1. {modulo 3} over 1, 2, 3, ... gives 1, 2, 0, 1, 2, 0,
  // ..., which makes it a handy cycling counter as well as an arithmetic play.

  // fmod rather than %, so the parameter is used as it was written rather than cut down to a
  // whole number - cutting it down turned any parameter below 1 into a modulo of zero. A
  // whole parameter still gives whole answers.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  if ( ! $pqParm )
    $pqParm = 1;

  // The + 0 turns the float -0 that rounds a small negative value, and that fmod gives for a
  // negative multiple, into 0: printed, it read -0.

  return fmod ( $pqLoop, $pqParm ) + 0;

?>
