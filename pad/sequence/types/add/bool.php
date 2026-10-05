<?php

  // Membership test for the add sequence, position n + the parameter: a value is a term when
  // n - p is a position. build/check.php asks it for {keep add}, {remove add} and {flag
  // add}. Without it the check generated the sequence only as far as the candidate's own
  // position, where a negative parameter puts every value past it - the stored answers
  // recorded the misses.

  function pqBoolAdd ( $n, $p=0 ) {

    return pqBoolPosition ( is_numeric ( $n ) ? $n - $p : NULL );

  }

?>