<?php

  // Reads the tag's positional parameters past the first, which nothing read: {keep $t,
  // '1..10'} with $t 'prime' kept from the default run, {keep '1..10', $t} kept 1 to 10, and
  // {sequence '1..5', 'x'}, {sequence 5, 7} answered as if the second were not there, all
  // without a word.
  //
  // After one that named what to run - parm/name.php, a {pull}'s store - the next is the
  // first parameter of what it named, read as the first is read after a name the tag or its
  // prefix gives: {keep $t, '1..10'} is {keep:prime '1..10'}, {sequence 's', 'reverse'} is
  // {pull:s 'reverse'}. After a count, a range, a list or a type's parameter, one that names a
  // store, a type or an action is read as the option of that name would be: {keep '1..10',
  // $t} is {keep '1..10', prime}, a range played on by prime, and {reverse '1..5', 'sum'} is
  // {reverse '1..5', sum}. What is left is named by the strict check.
  //
  // So for the third as for the second, each read by what the one before it was: only the
  // second was read, and a third was named whatever it was - {pull 's', 'shift', 2} ended on
  // "a parameter '2' that nothing reads" where {pull:s 'shift', 2} gives 1 2, and {sequence
  // 'prime', 3, 'reverse'} on 'reverse' where {prime 3, 'reverse'} gives 5 3 2.

  // A first one nothing placed is put back afterwards for find/default.php, which names it
  // when the run found nothing else to run - and then the others are left to it: read in its
  // place, {sequence 'xyz', 'prime'} ran the primes. Beside a store pulled only a number can
  // be left, dropped as before - {make add=3, mySequence, 3}, parm/parm.php - and the others
  // are only named.

  $pqFindFirst = $pqFindParm;
  $pqFindLeft  = ( is_array ( $pqFindFirst ) or (string) $pqFindFirst !== '' );

  if ( $pqFindLeft and ! $pqPull and ! $pqSeq )
    return;

  // After a name, the parameter is first read as the first parameter of that one alone - the
  // action's kind of parameter (parm/kind.php), the type's parameter or count (parm/count.php)
  // - and the second after a first that named what to run then as the first parameter of a
  // tag that names it, the whole of parm/: nothing else is settled by a parameter yet. A
  // later one goes no further: a type made of the first, the range of {sequence '1..3', 's',
  // 'reverse'}, is no type the name gave. What is left, and every one after a count, a range,
  // a list or a parameter, is read as a name of an option.

  $pqFindSecond = TRUE;

  foreach ( $padOpt [$pad] as $pqFindAt => $pqFindParm ) {

    if ( $pqFindAt < 2 or isset ( $pqFindTaken [$pqFindAt] ) or ( ! is_array ( $pqFindParm ) and (string) $pqFindParm === '' ) )
      continue;

    $pqFindPlace    = $pqFindAt;
    $pqFindAfter    = $pqFindLeft ? FALSE : $pqFindNamed;
    $pqFindNamed    = FALSE;
    $pqFindAsOption = FALSE;

    // An action's kind of parameter is read after the shape of the value for the second, as
    // parm/quick.php reads it before parm/primary.php: a list there is the values, {sequence
    // 'reverse', [1, 2, 3]} as {reverse [1, 2, 3]}.

    if ( $pqFindAfter == 'action' and ! $pqFindSecond ) {

      include PQ . 'inits/find/parm/kind.php';

      if ( $pqFindKind ) {
        $pqActionParm = is_array ( $pqFindParm ) ? ( pqActionList ( $pqAction, $pqFindParm ) ?? '' ) : $pqFindParm;
        $pqFindParm   = '';
      }

    } elseif ( $pqFindAfter == 'type' and padFileExists ( PT . "$pqSeq/flags/parm" ) ) {

      $pqParm     = $pqFindParm;
      $pqFindParm = '';

    } elseif ( $pqFindAfter == 'type' ) {

      $pqFindPositional = TRUE;

      include PQ . 'inits/find/parm/count.php';

    }

    if ( ( is_array ( $pqFindParm ) or (string) $pqFindParm !== '' ) and $pqFindAfter and $pqFindSecond ) {

      $pqFindPositional = TRUE;

      include PQ . 'inits/find/parm/quick.php';
      include PQ . 'inits/find/parm/primary.php';
      include PQ . 'inits/find/parm/name.php';
      include PQ . 'inits/find/parm/parm.php';

    } elseif ( ( is_array ( $pqFindParm ) or (string) $pqFindParm !== '' ) and ! $pqFindLeft ) {

      $pqFindAsOption = TRUE;

      if ( ! $pqSeq and ! $pqPull )
        include PQ . 'inits/find/parm/quick.php';

      include PQ . 'inits/find/parm/name.php';
      include PQ . 'inits/find/parm/store.php';

    }

    $pqFindSecond = FALSE;

    if ( ( is_array ( $pqFindParm ) or (string) $pqFindParm !== '' ) and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
      padError ( "the tag {" . $padTag [$pad] . "} was given "
               . ( is_array ( $pqFindParm ) ? "a list as a parameter" : "a parameter '$pqFindParm'" ) . " that nothing reads" );

  }

  $pqFindParm = $pqFindFirst;

?>
