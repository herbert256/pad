<?php

  // Build strategy 'loop' for the chance sequence: one random draw per candidate, returning
  // TRUE to let the loop value through and FALSE to drop it, so the result is a random
  // thinning of the range rather than a formula.
  //
  // The parameter is read either as a percentage, {chance '25%'} for a one-in-four hit, or
  // as a plain number N for a 1-in-N draw. build/one.php turns the TRUE back into the loop
  // value, so the terms are the surviving counter values; rows= with a generous try= is the
  // way to ask for a fixed number of hits, since a run of misses otherwise exhausts the try
  // limit.

  if ( str_contains ( $pqParm, '%' ) ) {

    $pqChance = str_replace('%', '', $pqParm );

    if  ( mt_rand ( 1, 100 ) <= $pqChance  ) return TRUE;
    else                                     return FALSE;

  } else {

    if  ( mt_rand ( 1, $pqParm ) == 1 ) return TRUE;
    else                                return FALSE;

  }

?>