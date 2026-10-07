<?php

  // Membership test for the modulo sequence, fmod(n, p) for n from 1 up - 1, 2, 0, 1, 2, 0
  // for modulo=3. build/check.php asks it for {keep modulo}, {remove modulo} and {flag
  // modulo}; without it the check looked for x among the first x terms, where 0 never is -
  // it first comes at position p - so 0 was no term of modulo=3, nor 0.5 of modulo=2.5.
  //
  // For a whole p the terms are the whole numbers from 0 up to p - 1. For a fractional one
  // they are what fmod makes: a whole x below p is the term at position x, and any other x
  // can only come from a position x + k * p, so those are asked for k up to
  // $padSeqDefaultTries. Asking the positions 1 to $padSeqDefaultTries instead missed every
  // term made further out - 20000 is the 20000th term of modulo=1000000.5 - and 0.5, which
  // first comes at position 1000001. A parameter of nothing or 0 is the 1 loop.php reads it as.

  function pqBoolModulo ( $x, $p=0 ) {

    if ( ! is_numeric ( $x ) )
      return FALSE;

    $p = ( is_numeric ( $p ) and $p != 0 ) ? abs ( $p + 0 ) : 1;

    if ( $x < 0 or $x >= $p )
      return FALSE;

    if ( pqBoolWhole ( $p ) )
      return pqBoolWhole ( $x );

    $tries = $GLOBALS ['padSeqDefaultTries'] ?? 10000;

    if ( pqBoolWhole ( $x ) and $x >= 1 )
      return TRUE;

    for ( $k = 0; $k <= $tries; $k++ ) {

      $n = round ( $x + $k * $p );

      if ( $n >= PHP_INT_MAX )
        break;

      if ( $n >= 1 and fmod ( $n, $p ) == $x )
        return TRUE;

    }

    return FALSE;

  }

?>
