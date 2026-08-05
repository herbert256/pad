<?php

  // prepend='seq' - puts every value of each named stored sequence in front of the
  // current one, keeping the store's own order (it unshifts them in reverse). Duplicates
  // are kept and the result is renumbered.

   foreach ( $pqActionList as $pqPrependKey ) {
    $pqPrependReverse = array_reverse($pqStore [$pqPrependKey]);
    foreach ($pqPrependReverse as $pqPrependValue)
      array_unshift ($pqResult, $pqPrependValue);
  }

?>