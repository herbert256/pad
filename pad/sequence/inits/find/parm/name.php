<?php

  // Reads a leftover first parameter that is a name as the store, the type or the action it
  // names - in that order, as find/prefix.php and find/tag.php prefer them - for the forms
  // that name what to run by a value rather than by an option: {sequence 'prime'},
  // {sequence $type, rows=3} with $type 'prime', {sequence 's'} for a store, {action 's',
  // reverse}, {keep 'odd', s}. Each was refused as "is not a sequence type, a store or an
  // action", and a template could not choose a type from a variable at all.
  //
  // Only what is still to be settled takes it, and only a plain name: pqSeq () and pqAction ()
  // keep a value from reaching a path. A parameter a type or an action has already claimed -
  // parm/primary.php, {repeat 'prime'} - is no longer left over here.

  if ( ! is_string ( $pqFindParm ) or ! preg_match ( '/^[A-Za-z][A-Za-z0-9_]*$/', $pqFindParm ) )
    return;

  // What it named is kept in $pqFindNamed - store, type or action - for parm/second.php,
  // which reads the next parameter as that one's first.

  // The store the tag pulls, named again, is that store: {pull:s 's'} was named as a second
  // store nothing reads.

  if ( ( ! $pqPull or $pqPull === $pqFindParm ) and isset ( $pqStore [$pqFindParm] ) ) {

    $pqPull      = $pqFindParm;
    $pqFindNamed = 'store';

  } elseif ( ! $pqSeq and pqSeq ( $pqFindParm ) ) {

    $pqSeq       = $pqFindParm;
    $pqParm      = TRUE;
    $pqFindNamed = 'type';

  } elseif ( ! $pqAction and ! $pqFindAsOption and pqAction ( $pqFindParm ) ) {

    $pqAction    = $pqFindParm;
    $pqFindNamed = 'action';

  // An action named while the tag's own is settled - or, $pqFindAsOption, after a count, a
  // range or a list, where parm/second.php reads a name as the option of that name - is read
  // as that option, parm/as.php, after the options written before it, and names nothing the
  // next parameter is the first one of: {reverse '1..5', 'sum'} and {action:reverse s, $a}
  // with $a 'sum' ended on "a parameter 'sum' that nothing reads", where {reverse '1..5', sum}
  // gives 15, and {sequence '1..5', sort, 'reverse'} reversed before it sorted.

  } elseif ( pqAction ( $pqFindParm ) )

    include PQ . 'inits/find/parm/as.php';

  // A type named while the tag's own is settled plays on it, as the option of that name does -
  // plays/add.php, as find/add.php adds one: {reverse 'prime', 'even'} and {prime 'even'} ended
  // on "The prime sequence takes no parameter, and 'even' is no count, range or list of it",
  // where {prime even} gives the even of each prime.

  elseif ( pqSeq ( $pqFindParm ) ) {

    $padPrmName  = $pqFindParm;
    $padPrmValue = TRUE;

    include PQ . 'plays/add.php';

  } else

    return;

  $pqFindParm = '';

?>
