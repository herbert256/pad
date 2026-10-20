<?php

  // onlyNow='seq' - keeps the values of the current sequence that are absent from the
  // named stores - every one of them, 'b|c', as intersection takes them all - discarding the
  // stores' own extra values. onlyStore is the mirror image and difference is the two
  // together. A name that is no store is passed over; with none at all nothing happens -
  // comparing against a store that is not there ended the request. Only the first of several
  // stores was read: {pull:a onlyNow='b|c'} kept c's values.

  $pqOnlyStores = [];

  foreach ( $pqActionList as $pqOnlyName )
    if ( isset ( $pqStore [$pqOnlyName] ) )
      $pqOnlyStores [] = $pqStore [$pqOnlyName];

  if ( count ( $pqOnlyStores ) )
    $pqResult = array_diff ( $pqResult, ...$pqOnlyStores );

?>
