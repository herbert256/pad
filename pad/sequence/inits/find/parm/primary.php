<?php

  // Places the leftover first parameter in the two unambiguous cases: exactly one of a
  // sequence type or an action is known, so the parameter can only belong to that one.
  //
  // Clears $pqFindParm once placed, leaving the case where both are known - or neither - to
  // find/parm/parm.php at the end of find/.

  // A 0 is a parameter like any other - {loop 0} - so the test is for nothing given.

  if ( (string) $pqFindParm !== '' and $pqSeq and ! $pqAction )
    if ( padFileExists ( PT . "$pqSeq/flags/parm" ) ) {
      $pqParm     = $pqFindParm;
      $pqFindParm = '';
    } else
      include PQ . 'inits/find/parm/count.php';

  // The action takes it when it is the action's kind of parameter: a store or a list for one
  // of actions/double/ and merge/, a count for one of actions/parm/. A store named for any
  // other is the store acted on, and a parameter for an action that takes none is left for
  // parm/name.php, which reads the store or the type it names, and parm/parm.php, which names
  // the rest: {sum 's'} summed the default 1..10 and answered 55, {sum 'abc'} as well, and
  // {action:first 'nums'} ended on "first= takes a count, not 'nums'".

  if ( (string) $pqFindParm !== '' and ! $pqSeq and $pqAction )
    if ( padFileExists ( PQ . "actions/double/$pqAction" ) or padFileExists ( PQ . "actions/merge/$pqAction" )
         or ( padFileExists ( PQ . "actions/parm/$pqAction" ) and ! ( is_string ( $pqFindParm ) and isset ( $pqStore [$pqFindParm] ) ) ) ) {
      $pqActionParm = $pqFindParm;
      $pqFindParm   = '';
    }

?>
