<?php

  // Function build for thueMorse: pqThueMorse($n) is the nth term of the Thue-Morse sequence,
  // whether n written in binary has an odd number of ones - 1, 1, 0, 1, 0, 0, 1, 1, 0, ... from
  // n = 1, 0 at position 0 - the sequence that never repeats a block three times in a row.
  // evil and odious are the positions of its 0s and its 1s. A position below 0, or between
  // two, has no term.

  function pqThueMorse ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    return substr_count ( decbin ( (int) $n ), '1' ) % 2;

  }

?>
