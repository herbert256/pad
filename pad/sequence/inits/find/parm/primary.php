<?php

  // Places the leftover first parameter in the two unambiguous cases: exactly one of a
  // sequence type or an action is known, so the parameter can only belong to that one.
  //
  // Clears $pqFindParm once placed, leaving the case where both are known - or neither - to
  // find/parm/parm.php at the end of find/.

  // A 0 is a parameter like any other - {loop 0} - so the test is for nothing given. A list is
  // given too: cast to text it ended the request on "Array to string conversion" - {prime
  // [3,5,7]}, {keep:prime [1,2,3,4,5]}, {add [1,2]}.

  if ( ( is_array ( $pqFindParm ) or (string) $pqFindParm !== '' ) and $pqSeq and ! $pqAction )
    if ( padFileExists ( PT . "$pqSeq/flags/parm" ) ) {
      $pqParm     = $pqFindParm;
      $pqFindParm = '';
    } else
      include PQ . 'inits/find/parm/count.php';

  // The action takes it when it is the action's kind of parameter, parm/kind.php: a store or a
  // list for one of actions/double/ and merge/, a count for one of actions/parm/. Anything
  // else is left for parm/name.php, which reads the store or the type it names, and
  // parm/parm.php, which names the rest: {sum 's'} summed the default 1..10 and answered 55,
  // {sum 'abc'} as well, and {action:first 'nums'} ended on "first= takes a count, not 'nums'".

  if ( ( is_array ( $pqFindParm ) or (string) $pqFindParm !== '' ) and ! $pqSeq and $pqAction ) {

    include PQ . 'inits/find/parm/kind.php';

    if ( $pqFindKind ) {
      $pqActionParm = is_array ( $pqFindParm ) ? ( pqActionList ( $pqAction, $pqFindParm ) ?? '' ) : $pqFindParm;
      $pqFindParm   = '';
    }

  }

?>
