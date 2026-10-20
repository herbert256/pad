<?php

  // A first parameter left for a type or an action that does not take it: nothing reads it,
  // and the strict check says so - {sequence prime, sum, 3} summed the ten primes in silence,
  // and {sum 'abc'} the default 1..10. The action is named by what it takes, parm/kind.php:
  // {reverse 'abc'} is no store for reverse, which takes one, where "takes no parameter" was
  // said of every action.

  if ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) {

    $pqFindWhat = is_scalar ( $pqFindParm ) ? $pqFindParm : gettype ( $pqFindParm );

    // A store beside the store the tag pulls is a second store, which nothing reads: {a:sum
    // 'b'} said "'b' is no store, type, count or range" of the store b.

    if ( $pqPull and is_string ( $pqFindParm ) and isset ( $pqStore [$pqFindParm] ) )
      padError ( "the tag {" . $padTag [$pad] . "} pulls the store $pqPull, and '$pqFindWhat' is a second store, which nothing reads" );
    elseif ( $pqSeq )
      padError ( "The $pqSeq sequence takes no parameter, and '$pqFindWhat' is no count, range or list of it" );
    elseif ( padFileExists ( PQ . "actions/parm/$pqAction" ) )
      padError ( "The $pqAction action takes a count, and '$pqFindWhat' is no count, store, type or range for it" );
    elseif ( padFileExists ( PQ . "actions/double/$pqAction" ) or padFileExists ( PQ . "actions/merge/$pqAction" ) )
      padError ( "The $pqAction action takes a store or a list, and '$pqFindWhat' is no store, type, count or range for it" );
    else
      padError ( "The $pqAction action takes no parameter, and '$pqFindWhat' is no store, type, count or range for it" );

  }

  $pqFindParm = '';

?>
