<?php

  // Runs a sequence action named with the action: prefix, the handler for kind 'action'.
  //
  // The name and the collected arguments go to the sequence subsystem through $pqSetAction /
  // $pqSetParms, which is what eval/parms/sequence.php does for the same case when the name is
  // reached without a prefix.
  //
  // The tag form of the same prefix is types/action.php. Only that half existed, so
  // {action:reverse} worked as a tag while the expression form ended the request on a missing
  // include.

  if ( ! pqAction ( $name ) ) {

    global $padCheckSyntax;

    return ( $padCheckSyntax ) ? padError ( "Action '$name' is not a sequence action" ) : '';

  }

  // The first argument is what the action runs over. A value piped in is that argument when
  // the call is written without one, as eval/parms/sequence.php takes it: [3,1,2] |
  // action:sort passed the list through unsorted while [3,1,2] | sequence:sort sorted it, and
  // so is the name of a store. Another single value - 'z' | action:reverse - has nothing to
  // act on, and passes through untouched, as it does with nothing piped and no arguments: it
  // used to answer '', swallowing its input, which is the one thing a pipe must never do to
  // a value.

  if ( ! $count and pqActionPiped ( $value ) ) {
    $parm  = [ $value ];
    $count = 1;
  }

  if ( ! $count )
    return $value;

  $pqSetAction = $name;
  $pqSetParms  = $parm;

  return include PQ . 'start/eval/action.php';

?>
