<?php

  // Membership test for the modulo sequence, fmod(n, p) for n from 1 up - 1, 2, 0, 1, 2, 0
  // for modulo=3. build/check.php asks it for {keep modulo}, {remove modulo} and {flag
  // modulo}; without it the check looked for x among the first x terms, where 0 never is -
  // it first comes at position p - so 0 was no term of modulo=3, nor 0.5 of modulo=2.5.
  //
  // For a whole p the terms are the whole numbers from 0 up to p - 1. For a fractional one
  // they are what fmod makes, so the first $padSeqDefaultTries positions are asked, the
  // reach a generated run has by default. A parameter of nothing or 0 is the 1 loop.php
  // reads it as.

  function pqBoolModulo ( $x, $p=0 ) {

    if ( ! is_numeric ( $x ) )
      return FALSE;

    $p = ( is_numeric ( $p ) and $p != 0 ) ? abs ( $p + 0 ) : 1;

    if ( $x < 0 or $x >= $p )
      return FALSE;

    if ( pqBoolWhole ( $p ) )
      return pqBoolWhole ( $x );

    $tries = $GLOBALS ['padSeqDefaultTries'] ?? 10000;

    for ( $n = 1; $n <= $tries; $n++ )
      if ( fmod ( $n, $p ) == $x )
        return TRUE;

    return FALSE;

  }

?>
