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

  // 2n - 1 as n + (n - 1), whole while the term fits: 2n is 2^63, past the range, for the
  // position whose term is PHP_INT_MAX itself, and the run made none.

  $pqFrom = pqBoolWhole ( $pqFrom ) ? (int) $pqFrom + ( (int) $pqFrom - 1 ) : $pqFrom * 2 - 1;

  if ( $pqTo != PHP_INT_MAX )
    $pqTo = pqBoolWhole ( $pqTo ) ? (int) $pqTo + ( (int) $pqTo - 1 ) : $pqTo * 2 - 1;

  // fmod, as from= doubled may have left the integer range - % ended the request on the float.

  // An integer's parity by %: fmod reads an integer past 2^53 as the float next to it, and
  // from=4503599627370497 - the odd number 9007199254740993 - was taken for even and skipped.

  if ( is_int ( $pqFrom ) ? $pqFrom % 2 == 0 : ! fmod ( $pqFrom, 2 ) )
    $pqFrom++;

?>
