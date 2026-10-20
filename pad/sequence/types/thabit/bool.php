<?php

  // Membership test for thabit, 2, 5, 11, 23, 47, ...: x is a term when x + 1 is three times a
  // power of 2 - x leaves 2 divided by 3, and the quotient one more is a power of 2, tested
  // without forming x + 1, which passes the integer range for its largest value. build/check.php
  // asks it for {keep thabit}, {remove thabit} and {flag thabit}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolThabit ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 2 or $x % 3 != 2 )
      return FALSE;

    $power = intdiv ( (int) $x, 3 ) + 1;

    return ( $power & ( $power - 1 ) ) == 0;

  }

?>
