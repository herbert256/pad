<?php

  // difference='seq' - the symmetric difference: the values found only in the current
  // sequence followed by the values found only in the named store, produced by running
  // onlyNow.php and onlyStore.php over the same input and concatenating their results.
  //
  // Both of those do nothing without a store to compare against, which left the two halves
  // holding the input and concatenated it with itself, so like them this does nothing when
  // no store was named - or when the one named was never pushed, which doubled the sequence
  // the same way: {sequence '1..3', difference='nope'} answered 1 2 3 1 2 3.

  if ( ! $pqActionParm or ! isset ( $pqStore [$pqActionParm] ) )
    return;

  $pqTmp = $pqResult;
  include PQ . 'actions/types/onlyNow.php';
  $pqTmp1 = $pqResult;
  $pqResult = $pqTmp;

  include PQ . 'actions/types/onlyStore.php';
  $pqTmp2 = $pqResult;

  foreach ( $pqTmp2 as $pqAppendKey )
    $pqTmp1 [] = $pqAppendKey;

  $pqResult = $pqTmp1;

?>
