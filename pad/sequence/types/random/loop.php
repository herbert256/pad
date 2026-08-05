<?php

  // Loop build for random: produces one random value per candidate, in one of four modes.
  //
  // A parameter ending in % is a probability - random='25%' returns TRUE a quarter of the
  // time and FALSE otherwise, and since build/one.php reads those as keep-the-loop-value
  // and skip-it, the type becomes a filter rather than a generator. A plain numeric
  // parameter is the same thing written as a one-in-n chance.
  //
  // With no parameter, a term list already sitting in $pqFixed (a stored or pulled
  // sequence, or the first pass of randomly=) is sampled instead; failing that a value is
  // drawn from the window init.php set up, snapped onto the increment grid when the
  // increment is not 1.

  if ( $pqParm ) {

    if ( str_contains ( $pqParm, '%' ) ) {
      $pqRandomPerc = str_replace ('%', '', $pqParm);
      if  ( mt_rand ( 1, 100 ) <= $pqRandomPerc ) return TRUE;
      else                                      return FALSE;
    }

    if ( is_numeric ( $pqParm ) )
      if  ( mt_rand ( 1, $pqParm ) == 1 ) return TRUE;
      else                                return FALSE;

  }

  if ( is_array ( $pqFixed ) and count ( $pqFixed ) )
     return $pqFixed [ array_rand ( $pqFixed, 1) ];

  if ( $pqInc != 1 ) {
    $pqRandomRand = rand ( 0, $pqRandomSteps ) * $pqInc;
    return $pqRandomStart + $pqRandomRand;
  }

  return rand ( $pqRandomStart, $pqRandomEnd );

?>