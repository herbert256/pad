<?php

  // onlyStore='seq' - the mirror image of onlyNow: replaces the sequence with the values
  // of the named stores - all of them, 'b|c', in the order named - that are absent from it,
  // so the result comes out of the stores, not out of the sequence being pulled. A name that is
  // no store is passed over; with none at all nothing happens - comparing against a store that
  // is not there ended the request. A value one store gave is not given again by a later one -
  // a store keeps its own repeats - where the stores were merged first and {pull:a
  // difference='b|c'} named the 4 and 5 both hold twice. Values are told apart as
  // pqValueKey () spells them, as dedup, merge and distinct tell them apart.

  $pqOnlyStores = [];

  foreach ( $pqActionList as $pqOnlyName )
    if ( isset ( $pqStore [$pqOnlyName] ) )
      $pqOnlyStores [] = $pqStore [$pqOnlyName];

  if ( ! count ( $pqOnlyStores ) )
    return;

  $pqOnlyKeys = [];

  foreach ( $pqResult as $pqOnlyValue )
    $pqOnlyKeys [ pqValueKey ( $pqOnlyValue ) ] = TRUE;

  $pqResult = [];

  foreach ( $pqOnlyStores as $pqOnlyOne ) {

    $pqOnlyHere = [];

    foreach ( $pqOnlyOne as $pqOnlyValue )
      if ( ! isset ( $pqOnlyKeys [ $pqOnlyKey = pqValueKey ( $pqOnlyValue ) ] ) ) {
        $pqResult   [] = $pqOnlyValue;
        $pqOnlyHere [$pqOnlyKey] = TRUE;
      }

    $pqOnlyKeys += $pqOnlyHere;

  }

?>
