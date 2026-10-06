<?php

  // Membership test for the divide sequence, position n / the parameter: a value is a term
  // when n * p is a position (with p 0 nothing is). build/check.php asks it for {keep
  // divide}, {remove divide} and {flag divide}. Without it the check generated the sequence
  // only as far as the candidate's own position, where every value of a divisor above 1
  // stands past it - the stored answers recorded the misses.

  function pqBoolDivide ( $n, $p=0 ) {

    if ( ! is_numeric ( $n ) or $p == 0 )
      return FALSE;

    return pqBoolPosition ( $n * $p );

  }

?>
