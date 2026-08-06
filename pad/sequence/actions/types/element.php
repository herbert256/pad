<?php

  // element=N - reduces the sequence to its Nth entry, counted from 1 in the current order
  // whatever the real keys are, and re-keyed to $pqActionKey. Without a parameter the
  // sequence is left untouched.
  //
  // A position the sequence does not reach - and any position at all when it is empty -
  // leaves nothing rather than reading past the end of the list.

  if ( ! $pqActionParm )
    return;

  $pqElements = array_values ( $pqResult );

  if ( isset ( $pqElements [$pqActionParm-1] ) )
    $pqResult = [ $pqActionKey => $pqElements [$pqActionParm-1] ];
  else
    $pqResult = [];

?>