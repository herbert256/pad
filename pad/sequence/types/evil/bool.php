<?php

  // Membership predicate and generation path for evil: pqBoolEvil($n) is TRUE when n written
  // in binary has an even number of ones - 0, 3, 5, 6, 9, 10, 12, 15, 17, 18, ... - the
  // numbers where the Thue-Morse sequence is 0. odious holds the others. The type's only
  // build, so the whole range is filtered through it.

  function pqBoolEvil ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    return substr_count ( decbin ( (int) $n ), '1' ) % 2 == 0;

  }

?>
