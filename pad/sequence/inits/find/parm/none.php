<?php

  // A first parameter left for a type that takes none: nothing reads it, and the strict check
  // says so - {sequence prime, sum, 3} summed the ten primes in silence.

  if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
    padError ( "The $pqSeq sequence takes no parameter, and '"
             . ( is_scalar ( $pqFindParm ) ? $pqFindParm : gettype ( $pqFindParm ) ) . "' is no count, range or list of it" );

  $pqFindParm = '';

?>
