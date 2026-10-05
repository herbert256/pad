<?php

  // element=N - reduces the sequence to its Nth entry, counted from 1 in the current order
  // whatever the real keys are. Without a parameter the sequence is left untouched.
  //
  // The entry keeps its own key, as first, last and the other selections do, so negative -
  // which takes the entries whose keys the selection does not hold - gives every entry but
  // the Nth. Re-keyed to the first key, element=2, negative over 1 to 6 dropped the 1 and
  // answered 2 3 4 5 6.
  //
  // A position the sequence does not reach - and any position at all when it is empty -
  // leaves nothing rather than reading past the end of the list.

  if ( ! $pqActionParm )
    return;

  // A position is a number: element='abc' ended the request on string - int. Strict mode
  // names it, and the sequence is left as it was.

  if ( ! is_numeric ( $pqActionParm ) ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "element= takes a position, not '$pqActionParm'" );
    return;
  }

  $pqElementKeys = array_keys ( $pqResult );

  if ( isset ( $pqElementKeys [$pqActionParm-1] ) ) {
    $pqElementKey = $pqElementKeys [$pqActionParm-1];
    $pqResult     = [ $pqElementKey => $pqResult [$pqElementKey] ];
  } else
    $pqResult = [];

?>