<?php

  // Membership test for the loop sequence, the counter 1, 2, 3 ...: a whole number from 1
  // up, and with a numeric parameter - the row count the type reads it as - no more than
  // that. build/check.php asks it for {keep loop}, {remove loop} and {flag loop}; without
  // it the check read the 10,000 terms of the PADloop table and ignored the 4 of loop=4.

  function pqBoolLoop ( $n, $p=0 ) {

    if ( ! pqBoolPosition ( $n ) )
      return FALSE;

    return ! is_numeric ( $p ) or $p < 1 or $n <= $p;

  }

?>
