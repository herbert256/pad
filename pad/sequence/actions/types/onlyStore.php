<?php

  // onlyStore='seq' - the mirror image of onlyNow: replaces the sequence with the values
  // of the named stores - all of them, 'b|c', in the order named - that are absent from it,
  // so the result comes out of the stores, not out of the sequence being pulled. A name that is
  // no store is passed over; with none at all nothing happens - comparing against a store that
  // is not there ended the request.

  $pqOnlyStores = [];

  foreach ( $pqActionList as $pqOnlyName )
    if ( isset ( $pqStore [$pqOnlyName] ) )
      $pqOnlyStores [] = $pqStore [$pqOnlyName];

  if ( count ( $pqOnlyStores ) )
    $pqResult = array_diff ( array_merge ( ...$pqOnlyStores ), $pqResult );

?>
