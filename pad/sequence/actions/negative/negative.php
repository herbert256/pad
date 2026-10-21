<?php

  // Implements the negative option: replaces an action's result with everything the
  // action did NOT select, taken from the pre-action snapshot $pqActionStart - so
  // {pull:nums first=5, negative} yields all but the first five. Does nothing unless
  // $pqNegative is set. The selection moves to $pqActionEnd and $pqResult is rebuilt by
  // keys.php when terms kept the 'x' keys assoc.php applied, or by values.php when the
  // action rebuilt the array and only values can still be matched.

  if ( ! $pqNegative )
    return;

  // An action that collapses the sequence into one value selects nothing, so there is nothing
  // to invert and the value stands: the total took the first term's key, and "not selected"
  // was every term but the first - {sequence '1..6', sum, negative} gave 2 3 4 5 6.

  if ( in_array ( $pqAction, [ 'sum', 'product', 'average', 'median', 'count', 'distinct' ] ) )
    return;

  // A term the action kept carries the x of its key however it moved it, and one it added - a
  // store a splice put in, an append - carries none, so the terms it did not select are the
  // ones whose key is gone as long as one term kept its key. Only a result rebuilt whole - a
  // sort, a merge - is matched by value. Any key without the x sent every term to the values:
  // {sequence '1;1;2', splice='0|1|s', negative} had no 1, the 1 that stayed taking the
  // other's place, and kept every value it found anywhere in the result.

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV )
    if ( str_starts_with ( (string) $padK, 'x' ) )
      return include PQ . 'actions/negative/keys.php';

  return include PQ . 'actions/negative/values.php';

?>
