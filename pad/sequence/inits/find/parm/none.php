<?php

  // A first parameter left for a type or an action that takes none: nothing reads it, and the
  // strict check says so - {sequence prime, sum, 3} summed the ten primes in silence, and
  // {sum 'abc'} the default 1..10.

  if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
    if ( $pqSeq )
      padError ( "The $pqSeq sequence takes no parameter, and '"
               . ( is_scalar ( $pqFindParm ) ? $pqFindParm : gettype ( $pqFindParm ) ) . "' is no count, range or list of it" );
    else
      padError ( "The $pqAction action takes no parameter, and '"
               . ( is_scalar ( $pqFindParm ) ? $pqFindParm : gettype ( $pqFindParm ) ) . "' is no store, type, count or range for it" );

  $pqFindParm = '';

?>
