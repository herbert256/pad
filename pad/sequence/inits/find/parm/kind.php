<?php

  // Whether the parameter $pqFindParm is the kind of parameter the action $pqAction takes, in
  // $pqFindKind - the one rule parm/primary.php, parm/parm.php, parm/second.php and {resume}'s
  // inits/set.php give an action its parameter by. An action that counts - actions/parm/,
  // splice's offset among them - takes a value that names no store and no type: a count,
  // 'pos|len', both. An action that takes stores - actions/double/ and merge/ - takes a list
  // or the name of a store, 'a|b' for two. An action that takes none takes nothing.
  //
  // What the action does not take is read as any first parameter is: a store or a type it
  // names is the run's, parm/name.php, a number is how many rows, parm/quick.php, and what is
  // left is named by parm/none.php. An action that takes stores took anything, and {reverse
  // 'prime'} reversed 1 to 10 where {sum 'prime'} sums the primes, {reverse 'abc'}, {append
  // 'abc'} and {append 5} were dropped without a word; one that counts took a type's name,
  // {action:first 'prime'} ended on "first= takes a count, not 'prime'".

  if ( padFileExists ( PQ . "actions/parm/$pqAction" ) )

    $pqFindKind = ( is_scalar ( $pqFindParm )
                    and ! ( is_string ( $pqFindParm ) and ( isset ( $pqStore [$pqFindParm] ) or pqSeq ( $pqFindParm ) ) ) );

  elseif ( padFileExists ( PQ . "actions/double/$pqAction" ) or padFileExists ( PQ . "actions/merge/$pqAction" ) ) {

    $pqFindKind = is_array ( $pqFindParm );

    if ( is_string ( $pqFindParm ) and $pqFindParm !== '' ) {
      $pqFindKind = TRUE;
      foreach ( padExplode ( $pqFindParm, '|' ) as $pqFindOne )
        if ( ! isset ( $pqStore [$pqFindOne] ) )
          $pqFindKind = FALSE;
    }

  } else

    $pqFindKind = FALSE;

?>
