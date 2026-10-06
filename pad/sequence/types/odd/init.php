<?php

  // Range setup for odd, run before generation: step by twice the increment and start on an
  // odd value, so the iterator only ever offers odd candidates.
  //
  // Reached through sequence/inits/init.php, or through plays/init.php when odd is used as
  // a play, which restores the range afterwards. from= and to= are scaled the way
  // even/init.php scales them, so they count terms rather than name values: the nth odd
  // number is 2n-1, so from=3 starts at 5.

  // increment= counts terms as from= and to= do, so it is doubled with them: set to 2
  // whatever was asked, {sequence odd, increment=3} counted every term where square counts
  // every third. One below 1 is left as it is, for the loop to refuse as it does for every
  // type.

  if ( $pqInc >= 1 )
    $pqInc = 2 * $pqInc;

  $pqFrom = $pqFrom * 2 - 1;

  if ( $pqTo != PHP_INT_MAX )
    $pqTo = $pqTo * 2 - 1;

  // fmod, as from= doubled may have left the integer range - % ended the request on the float.

  if ( ! fmod ( $pqFrom, 2 ) )
    $pqFrom++;

?>
