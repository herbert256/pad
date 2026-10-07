<?php

  // Function build for prime: pqPrime($n) returns the nth prime - 2, 3, 5, 7, 11, ...
  //
  // Not the default path, since pqBuild() prefers loop.php; reached with build=function and
  // called by prime/make.php. Loaded on every prime build anyway, because build/include.php
  // pulls in a type's function.php unconditionally.
  //
  // The 1,229 primes below 10,000 come from the PADprime table; past it the list grows from the
  // table's end, each candidate put to pqBoolPrime(), which divides only up to its square
  // root, and the list is kept for the next call. It started from 2 for every term and
  // divided each candidate by every prime before it - 24 seconds at from=20000.

  function pqPrime ( int $number ) {

    static $primes = NULL;

    if ( $number < 1 )
      return FALSE;

    // A position past $padSeqMaxTries, the ceiling a run walks to, is not searched for: it
    // answers a float past the integer range, which ends the run as an overflowed term does,
    // as recaman does. Searched for, {sequence '9223372036854775807;1', make, prime} grew the
    // list of primes until the request was killed.

    if ( $number > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) )
      return (float) PHP_INT_MAX * 2;

    if ( $primes === NULL )
      $primes = defined ( 'PADprime' ) ? PADprime : [ 2 ];

    include_once PT . 'prime/bool.php';

    for ( $n = end ( $primes ) + 1; count ( $primes ) < $number; $n++ )
      if ( pqBoolPrime ( $n ) )
        $primes [] = $n;

    return $primes [$number - 1];

  }

?>
