<?php

  // intersection='seq' - keeps only the values that also occur in every named store, by
  // handing array_intersect to actions/function.php.

  // Every argument is another stored sequence: a number handed to array_intersect ended
  // the request on a TypeError. Strict mode names it, and the sequence is left as it was.

  foreach ( $pqActionList as $pqIntersectOne )
    if ( ! isset ( $pqStore [$pqIntersectOne] ) ) {
      if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
        padError ( "intersection= takes stored sequences, and '$pqIntersectOne' is none" );
      return $pqResult;
    }

  $pqFunction = 'array_intersect';

  $pqResult = include PQ . 'actions/function.php';

?>