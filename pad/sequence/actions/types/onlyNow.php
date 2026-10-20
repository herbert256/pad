<?php

  // onlyNow='seq' - keeps the values of the current sequence that are absent from the
  // named stores - every one of them, 'b|c', as intersection takes them all - discarding the
  // stores' own extra values. onlyStore is the mirror image and difference is the two
  // together. A name that is no store is passed over; with none at all nothing happens -
  // comparing against a store that is not there ended the request. Only the first of several
  // stores was read: {pull:a onlyNow='b|c'} kept c's values. Values are told apart as
  // pqValueKey () spells them, as dedup, merge and distinct tell them apart: compared as text
  // by array_diff, 1 and '1.0' were two values here and one there.

  $pqOnlyStores = [];

  foreach ( $pqActionList as $pqOnlyName )
    if ( isset ( $pqStore [$pqOnlyName] ) )
      $pqOnlyStores [] = $pqStore [$pqOnlyName];

  $pqOnlyKeys = [];

  foreach ( $pqOnlyStores as $pqOnlyOne )
    foreach ( $pqOnlyOne as $pqOnlyValue )
      $pqOnlyKeys [ pqValueKey ( $pqOnlyValue ) ] = TRUE;

  if ( count ( $pqOnlyStores ) )
    $pqResult = array_filter ( $pqResult, fn ( $v ) => ! isset ( $pqOnlyKeys [ pqValueKey ( $v ) ] ) );

?>
