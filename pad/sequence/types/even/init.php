<?php

  // Range setup for the even sequence, run before generation by sequence/inits/init.php.
  //
  // from= and to= count terms rather than values here, so both are doubled, and so is
  // the step; the loop then walks the even numbers directly and make.php has nothing left
  // to correct. from=3, to=5 therefore gives 6, 8, 10. The trailing odd nudge can only fire
  // for a fractional from=, since doubling an integer never leaves an odd value.
  //
  // plays/init.php runs this file too when even is used as a play, and restores the range
  // afterwards so the main sequence is unaffected.

  // increment= counts terms as from= and to= do, so it is doubled with them: set to 2
  // whatever was asked, {sequence even, increment=3} counted every term where square counts
  // every third. One below 1 is left as it is, for the loop to refuse as it does for every
  // type.

  if ( $pqInc >= 1 )
    $pqInc = 2 * $pqInc;

  $pqFrom = $pqFrom * 2;

  if ( $pqTo != PHP_INT_MAX )
    $pqTo = $pqTo * 2;

  // fmod, as from= doubled may have left the integer range - % ended the request on the float.

  // An integer's parity by %: fmod reads an integer past 2^53 as the float next to it. A
  // start past the float range has none: fmod ( INF, 2 ) is NAN, which PHP 8.5 refuses as a
  // truth - from=1e400 ended the request on "unexpected NAN value was coerced to bool" where
  // every other type answers an empty run.

  if ( is_int ( $pqFrom ) ? $pqFrom % 2 != 0 : ( is_finite ( $pqFrom ) and fmod ( $pqFrom, 2 ) ) )
    $pqFrom++;

?>
