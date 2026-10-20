<?php

  // Implements the negative option: replaces an action's result with everything the
  // action did NOT select, taken from the pre-action snapshot $pqActionStart - so
  // {pull:nums first=5, negative} yields all but the first five. Does nothing unless
  // $pqNegative is set. The selection moves to $pqActionEnd and $pqResult is rebuilt by
  // keys.php when the action kept the 'x' keys assoc.php applied, or by values.php when
  // it rebuilt the array and only values can still be matched.

  if ( ! $pqNegative )
    return;

  // An action that collapses the sequence into one value selects nothing, so there is nothing
  // to invert and the value stands: the total took the first term's key, and "not selected"
  // was every term but the first - {sequence '1..6', sum, negative} gave 2 3 4 5 6.

  if ( in_array ( $pqAction, [ 'sum', 'product', 'average', 'median', 'count', 'distinct' ] ) )
    return;

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV )
    if ( substr ( $padK, 0, 1 ) != 'x' )
      return include PQ . 'actions/negative/values.php';

  return include PQ . 'actions/negative/keys.php';

?>
