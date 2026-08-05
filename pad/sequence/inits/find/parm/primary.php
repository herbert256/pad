<?php

  // Places the leftover first parameter in the two unambiguous cases: exactly one of a
  // sequence type or an action is known, so the parameter can only belong to that one.
  //
  // Clears $pqFindParm once placed, leaving the case where both are known - or neither - to
  // find/parm/parm.php at the end of find/.

  if ( $pqFindParm and $pqSeq and ! $pqAction ) {
    $pqParm     = $pqFindParm;
    $pqFindParm = '';
  }

  if ( $pqFindParm and ! $pqSeq and $pqAction ) {
    $pqActionParm = $pqFindParm;
    $pqFindParm   = '';
  }

?>