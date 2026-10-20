<?php

  // Build strategy 'loop' for the multiple sequence, the one pqBuild() prefers: the loop
  // value rounded up to the next multiple of the parameter, giving 3, 6, 9, 12, ... for
  // {multiple 3}. init.php has already put the range on those multiples, so for a plain
  // build the rounding is the identity; make.php holds the same expression for use as a
  // play, and bool.php answers the membership questions.
  //
  // init.php refuses a step of zero, but one written as a range is drawn afresh for every
  // candidate and can come up zero here, where the answer is no term for this candidate.

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  // A parameter that is 0 - spelled 0, '0.0', '-0' or '00' - makes no term. ! $pqParm let the
  // strings '0.0', '-0' and '00' through, which are true, and rounding up to a multiple of it ended
  // the request on "Division by zero".

  if ( is_numeric ( $pqParm ) ? $pqParm == 0 : ! $pqParm )
    return FALSE;

  return pqCeilMultiple ( $pqLoop, $pqParm );

?>
