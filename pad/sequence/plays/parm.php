<?php

  // Resolves the parameter a play should use for the current candidate.
  //
  // Included as an expression by plays/plays.php, once per play per candidate, and returns
  // the parameter. A parm naming a stored sequence yields the term lining up with the
  // number of results so far, so parameter and result advance in step; a 'from..to' parm
  // is re-rolled for every term.

  if ( $pqParm and isset ( $pqStore [$pqParm] ) )
    $pqParm = $pqStore [$pqParm] [ count ( $pqResult ) ];

  if ( str_contains ( $pqParm, '..' ) and $pqSeq != 'range' )
    pqRandomParm ( $pqParm );

  return $pqParm;

?>