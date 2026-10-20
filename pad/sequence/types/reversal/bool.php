<?php

  // Membership test for reversal: a reversal ends on the first digit of the number reversed,
  // which is no 0, and every number that does not end on 0 is the reversal of its own
  // reversal - so the terms are 0 and the numbers that do not end on 0. build/check.php asks
  // it for {keep reversal}, {remove reversal} and {flag reversal}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolReversal ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    return $x == 0 or $x % 10 != 0;

  }

?>
