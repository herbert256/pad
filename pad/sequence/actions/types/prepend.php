<?php

  // prepend='seq' - puts every value of each named stored sequence in front of the
  // current one, keeping the store's own order (it unshifts them in reverse). Duplicates
  // are kept and the result is renumbered. A name that is not a store is skipped rather
  // than reversed as one.

  // One merge per store, in front of the sequence - the store's order, the integer keys
  // renumbered and the 'x' keys kept, as array_unshift () kept them. Unshifted one value at a
  // time, every call renumbered the whole array: two stores of 40,000 took 16 seconds.

  foreach ( $pqActionList as $pqPrependKey )
    if ( isset ( $pqStore [$pqPrependKey] ) )
      $pqResult = array_merge ( array_values ( $pqStore [$pqPrependKey] ), $pqResult );

?>
