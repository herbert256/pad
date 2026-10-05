<?php

  // Membership test for the subtract sequence, position n - the parameter: a value is a term
  // when n + p is a position. build/check.php asks it for {keep subtract}, {remove subtract}
  // and {flag subtract}. Without it the check generated the sequence only as far as the
  // candidate's own position, where every value stands past it - the stored answers recorded
  // the misses.

  function pqBoolSubtract ( $n, $p=0 ) {

    return pqBoolPosition ( is_numeric ( $n ) ? $n + $p : NULL );

  }

?>