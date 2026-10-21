<?php

  // Prefixes every key of $pqResult with 'x' before the actions run, keeping the
  // unprefixed copy in $pqActionStart. Actions that preserve keys leave the 'x' in
  // place, which is how order/ and negative/ later tell a key-preserving action from one
  // that rebuilt the array and can only be matched by value. The terms as the build made
  // them stay in $pqResultBuilt, for exits/extra/extra.php to tell which term a row is.

  $pqActionStart = $pqResult;
  $pqResultBuilt = $pqResult;

  // One action that totals the terms into a value - count, sum, product, average, median,
  // distinct - with nothing to invert reads no key, and the total is no term: the copy under x
  // keys, as large again as the terms, is left out. {sequence '1..1000000', count} took 141
  // MB, half of it this copy.

  if ( count ( $pqActions ) == 1 and ! $pqNegative
       and in_array ( $pqActions [ array_key_first ( $pqActions ) ] [0], [ 'sum', 'product', 'average', 'median', 'count', 'distinct' ] ) )
    return;

  $pqResult      = [];

  foreach ( $pqActionStart as $padK => $padV )
    $pqResult [ 'x' . $padK ] = $padV;

?>
