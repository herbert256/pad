<?php

  // onlyNow='seq' - keeps the values of the current sequence that are absent from the
  // named store, discarding the store's own extra values. onlyStore is the mirror image
  // and difference is the two together. Without a parameter nothing happens.

  if ( $pqActionParm )
    $pqResult = array_diff ( $pqResult, $pqStore [$pqActionParm] );

?>