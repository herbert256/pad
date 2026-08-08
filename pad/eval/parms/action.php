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

  if ( ! file_exists ( PA . "$name.php" ) )
    return padError ( "Action '$name' is not a sequence action" );

  // The first argument is what the action runs over, so with no arguments at all there is
  // nothing to act on and the piped value passes through untouched. This used to answer ''
  // - swallowing its input, which is the one thing a pipe must never do to a value.

  if ( ! $count )
    return $value;

  $pqSetAction = $name;
  $pqSetParms  = $parm;

  return include PQ . 'start/eval/action.php';

?>