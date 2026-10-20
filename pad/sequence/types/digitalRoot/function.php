<?php

  // Function build for digitalRoot: pqDigitalRoot($n) is what adding up the digits of n over
  // and over ends at - 1, 2, ..., 9, 1, 2, ... from n = 1, 0 at position 0 - which is n modulo
  // 9 with 9 in the place of 0. A position below 0, or between two, has no term.

  function pqDigitalRoot ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    $n = (int) $n;

    return $n == 0 ? 0 : 1 + ( $n - 1 ) % 9;

  }

?>
