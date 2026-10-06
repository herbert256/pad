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

    return pqBoolPosition ( $n / $p );

  }

?>
