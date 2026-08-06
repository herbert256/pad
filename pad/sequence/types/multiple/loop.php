<?php

  // Build strategy 'loop' for the multiple sequence, the one pqBuild() prefers: the loop
  // value rounded up to the next multiple of the parameter, giving 3, 6, 9, 12, ... for
  // {multiple 3}. init.php has already put the range on those multiples, so for a plain
  // build the rounding is the identity; make.php holds the same expression for use as a
  // play, and bool.php answers the membership questions.
  //
  // init.php refuses a step of zero, but one written as a range is drawn afresh for every
  // candidate and can come up zero here, where the answer is no term for this candidate.

  if ( ! $pqParm )
    return FALSE;

  return ceil ( $pqLoop / $pqParm) * $pqParm;

?>