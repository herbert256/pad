<?php

  // Range setup for odd, run before generation: step by two and start on an odd value, so
  // the iterator only ever offers odd candidates.
  //
  // Reached through sequence/inits/init.php, or through plays/init.php when odd is used as
  // a play, which restores the range afterwards. from= and to= are scaled the way
  // even/init.php scales them, so they count terms rather than name values: the nth odd
  // number is 2n-1, so from=3 starts at 5.

  $pqInc = 2;

  $pqFrom = $pqFrom * 2 - 1;

  if ( $pqTo != PHP_INT_MAX )
    $pqTo = $pqTo * 2 - 1;

  if ( ! ($pqFrom % 2) )
    $pqFrom++;

?>