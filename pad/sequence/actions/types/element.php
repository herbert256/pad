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
  // leaves nothing rather than reading past the end of the list. 0 is such a position: it
  // was taken for no parameter at all, so element=0 answered the whole sequence.

  if ( (string) $pqActionParm === '' )
    return;

  // A position is a whole number: element='abc' ended the request on string - int, and
  // element=2.5 on PHP's deprecation of a fractional array key. Strict mode names it, and
  // the sequence is left as it was.

  if ( ! is_numeric ( $pqActionParm ) or floor ( $pqActionParm ) != $pqActionParm ) {
    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( "element= takes a position, not '$pqActionParm'" );
    return;
  }

  $pqElementKeys = array_keys ( $pqResult );

  $pqElementAt   = (int) $pqActionParm - 1;

  if ( isset ( $pqElementKeys [$pqElementAt] ) ) {
    $pqElementKey = $pqElementKeys [$pqElementAt];
    $pqResult     = [ $pqElementKey => $pqResult [$pqElementKey] ];
  } else
    $pqResult = [];

?>