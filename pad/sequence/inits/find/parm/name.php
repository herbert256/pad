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

  if ( ! $pqPull and isset ( $pqStore [$pqFindParm] ) )

    $pqPull = $pqFindParm;

  elseif ( ! $pqSeq and pqSeq ( $pqFindParm ) ) {

    $pqSeq  = $pqFindParm;
    $pqParm = TRUE;

  } elseif ( ! $pqAction and pqAction ( $pqFindParm ) )

    $pqAction = $pqFindParm;

  else

    return;

  $pqFindParm = '';

?>
