<?php

  // intersection='seq' - keeps only the values that also occur in every named store. Values
  // are told apart as pqValueKey () spells them, as dedup, merge and distinct tell them
  // apart: compared as text by array_intersect, 1 and '1.0' were two values here and one
  // there. Each store is made a set of those keys once, so the cost is linear.

  // Every argument is another stored sequence: a number handed to array_intersect ended
  // the request on a TypeError. Strict mode names it, and the sequence is left as it was.

  foreach ( $pqActionList as $pqIntersectOne )
    if ( ! isset ( $pqStore [$pqIntersectOne] ) ) {
      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "intersection= takes stored sequences, and '$pqIntersectOne' is none" );
      return $pqResult;
    }

  foreach ( $pqActionList as $pqIntersectOne ) {
    $pqIntersectKeys = array_flip ( array_map ( 'pqValueKey', $pqStore [$pqIntersectOne] ) );
    $pqResult        = array_filter ( $pqResult, fn ( $v ) => isset ( $pqIntersectKeys [ pqValueKey ( $v ) ] ) );
  }

?>
