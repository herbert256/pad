<?php

  // Membership test for the negation sequence, -1, -2, -3, ...: a whole number of -1 or
  // less. build/check.php asks it for {keep negation}, {remove negation} and {flag
  // negation}. Without it the check read the 10,000 terms of the PADnegation table, which
  // ends at -10000 and does not climb, and so was taken as the whole sequence: -10001 and
  // every term below it were answered no.
  //
  // PHP_INT_MIN is -(2^63), the negation of a position past the integer range - the last term
  // a run makes is -9223372036854775807 - and is no member, as multiply=-1 has it: answered
  // yes, {sequence '-9223372036854775808;-5;5', keep, negation} kept it beside -5.

  function pqBoolNegation ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and $n <= -1 and $n > PHP_INT_MIN;

  }

?>
