<?php

  // Range setup for the even sequence, run before generation by sequence/inits/init.php.
  //
  // from= and to= count terms rather than values here, so both are doubled and the step is
  // set to 2; the loop then walks the even numbers directly and make.php has nothing left
  // to correct. from=3, to=5 therefore gives 6, 8, 10. The trailing odd nudge can only fire
  // for a fractional from=, since doubling an integer never leaves an odd value.
  //
  // plays/init.php runs this file too when even is used as a play, and restores the range
  // afterwards so the main sequence is unaffected.

  $pqInc = 2;

  $pqFrom = $pqFrom * 2;

  if ( $pqTo != PHP_INT_MAX )
    $pqTo = $pqTo * 2;

  if ( $pqFrom % 2 )
    $pqFrom++;

?>