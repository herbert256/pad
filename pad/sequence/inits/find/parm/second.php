<?php

  // Reads the tag's positional parameters past the first, which nothing read: {keep $t,
  // '1..10'} with $t 'prime' kept from the default run, {keep '1..10', $t} kept 1 to 10, and
  // {sequence '1..5', 'x'}, {sequence 5, 7} answered as if the second were not there, all
  // without a word.
  //
  // After a first one that named what to run - parm/name.php, a {pull}'s store - the second
  // is the first parameter of what it named, read as the first is read after a name the tag
  // or its prefix gives: {keep $t, '1..10'} is {keep:prime '1..10'}, {sequence 's', 'reverse'}
  // is {pull:s 'reverse'}. After a count, a range, a list or a type's parameter, a second
  // that names a store, a type or an action is read as the option of that name would be:
  // {keep '1..10', $t} is {keep '1..10', prime}, a range played on by prime. What is left -
  // a second nothing takes, a third - is named by the strict check.

  // A first one nothing placed is put back afterwards for find/default.php, which names it
  // when the run found nothing else to run - and then the others are left to it: read in its
  // place, {sequence 'xyz', 'prime'} ran the primes. Beside a store pulled the first is
  // dropped, as before - {make add=3, mySequence, 3} - and the others are only named.

  $pqFindFirst = $pqFindParm;
  $pqFindLeft  = ( is_array ( $pqFindFirst ) or (string) $pqFindFirst !== '' );

  if ( $pqFindLeft and ! $pqPull and ! $pqSeq )
    return;

  foreach ( $padOpt [$pad] as $pqFindAt => $pqFindParm ) {

    if ( $pqFindAt < 2 or isset ( $pqFindTaken [$pqFindAt] ) or ( ! is_array ( $pqFindParm ) and (string) $pqFindParm === '' ) )
      continue;

    if ( ! $pqFindLeft and $pqFindAt == 2 and $pqFindNamed ) {

      $pqFindPositional = TRUE;

      include PQ . 'inits/find/parm/quick.php';
      include PQ . 'inits/find/parm/primary.php';
      include PQ . 'inits/find/parm/name.php';
      include PQ . 'inits/find/parm/parm.php';

    } elseif ( ! $pqFindLeft and $pqFindAt == 2 ) {

      if ( ! $pqSeq and ! $pqPull )
        include PQ . 'inits/find/parm/quick.php';

      include PQ . 'inits/find/parm/name.php';

      if ( $pqSeq and pqSeq ( $pqFindParm ) ) {

        $padPrmName  = $pqFindParm;
        $padPrmValue = TRUE;

        include PQ . 'plays/add.php';

        $pqFindParm = '';

      }

    }

    if ( ( is_array ( $pqFindParm ) or (string) $pqFindParm !== '' ) and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
      padError ( "the tag {" . $padTag [$pad] . "} was given "
               . ( is_array ( $pqFindParm ) ? "a list as a parameter" : "a parameter '$pqFindParm'" ) . " that nothing reads" );

  }

  $pqFindParm = $pqFindFirst;

?>
