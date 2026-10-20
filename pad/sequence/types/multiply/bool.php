<?php

  // Membership test for the multiply sequence, position n * the parameter: a value is a term
  // when n / p is a position (with p 0 only 0 is). build/check.php asks it for {keep
  // multiply}, {remove multiply} and {flag multiply}. Without it the check generated the
  // sequence only as far as the candidate's own position, where a factor below 1 puts every
  // value past it - the stored answers recorded the misses.

  function pqBoolMultiply ( $n, $p=0 ) {

    if ( ! is_numeric ( $n ) )
      return FALSE;

    if ( $p == 0 )
      return $n == 0;

    // In whole numbers when both are whole, as pqBoolMultiple asks: divided in floating point,
    // odd numbers past 2^54 came out terms of multiply=2.

    // PHP_INT_MIN of multiply=-1 would stand at position 2^63, past the integer range, and is
    // no term: PHP_INT_MIN % -1 is 0, and intdiv ended the request on "Division of PHP_INT_MIN
    // by -1 is not an integer" - {sequence '-9223372036854775808;5', flag, multiply=-1}.

    if ( pqBoolWhole ( $n ) and pqBoolWhole ( $p ) and $p !== TRUE ) {
      if ( (int) $n == PHP_INT_MIN and (int) $p == -1 )
        return FALSE;
      return ( (int) $n % (int) $p == 0 and intdiv ( (int) $n, (int) $p ) >= 1 );
    }

    return pqBoolPosition ( $n / $p );

  }

?>
