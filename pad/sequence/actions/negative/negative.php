<?php

  // Implements the negative option: replaces an action's result with everything the
  // action did NOT select, taken from the pre-action snapshot $pqActionStart - so
  // {pull:nums first=5, negative} yields all but the first five. Does nothing unless
  // $pqNegative is set. The selection moves to $pqActionEnd and $pqResult is rebuilt by
  // keys.php when the action kept the 'x' keys assoc.php applied, or by values.php when
  // it rebuilt the array and only values can still be matched.

  if ( ! $pqNegative )
    return;

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV )
    if ( substr ( $padK, 0, 1 ) != 'x' )
      return include PQ . 'actions/negative/values.php';

  return include PQ . 'actions/negative/keys.php';

?>