<?php

  // prepend='seq' - puts every value of each named stored sequence in front of the
  // current one, keeping the store's own order (it unshifts them in reverse). Duplicates
  // are kept and the result is renumbered. A name that is not a store is skipped rather
  // than reversed as one.

   foreach ( $pqActionList as $pqPrependKey ) {
    if ( ! isset ( $pqStore [$pqPrependKey] ) ) continue;
    $pqPrependReverse = array_reverse($pqStore [$pqPrependKey]);
    foreach ($pqPrependReverse as $pqPrependValue)
      array_unshift ($pqResult, $pqPrependValue);
  }

?>