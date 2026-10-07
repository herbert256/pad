<?php

  // Resolves the parameter a play should use for the current candidate.
  //
  // Included as an expression by plays/plays.php, once per play per candidate, and returns
  // the parameter. A parm naming a stored sequence yields the term lining up with the
  // number of results so far, so parameter and result advance in step; a 'from..to' parm
  // is re-rolled for every term.
  //
  // A store shorter than the sequence has no term for the results past its end: NULL comes
  // back, and plays/plays.php ends the run there, as build/store.php does for the
  // sequence's own parameter - reading past the end ended the request on the undefined key.

  // A store is named by a name: a parameter that is a number - a stored 1e20 - is no key to
  // look up, and as one ended the request on "The float 1.0E+20 is not representable as an int".

  if ( is_string ( $pqParm ) and $pqParm and isset ( $pqStore [$pqParm] ) )
    $pqParm = $pqStore [$pqParm] [ count ( $pqResult ) ] ?? NULL;

  if ( $pqParm === NULL )
    return NULL;

  if ( str_contains ( $pqParm, '..' ) and $pqSeq != 'range' )
    pqRandomParm ( $pqParm );

  return $pqParm;

?>
