<?php

  // Membership predicate and generation path for odious: pqBoolOdious($n) is TRUE when n
  // written in binary has an odd number of ones - 1, 2, 4, 7, 8, 11, 13, 14, 16, 19, ... -
  // the numbers where the Thue-Morse sequence is 1. evil holds the others. The type's only
  // build, so the whole range is filtered through it.

  function pqBoolOdious ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    return substr_count ( decbin ( (int) $n ), '1' ) % 2 == 1;

  }

?>
