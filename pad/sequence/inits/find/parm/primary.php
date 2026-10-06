<?php

  // Places the leftover first parameter in the two unambiguous cases: exactly one of a
  // sequence type or an action is known, so the parameter can only belong to that one.
  //
  // Clears $pqFindParm once placed, leaving the case where both are known - or neither - to
  // find/parm/parm.php at the end of find/.

  // A 0 is a parameter like any other - {loop 0} - so the test is for nothing given.

  if ( (string) $pqFindParm !== '' and $pqSeq and ! $pqAction ) {
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( (string) $pqFindParm !== '' and ! $pqSeq and $pqAction ) {
    $pqActionParm = $pqFindParm;
    $pqFindParm   = '';
  }

?>
