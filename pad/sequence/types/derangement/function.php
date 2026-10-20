<?php

  // Function build for derangement: pqDerangement($n) is the number of ways to put n things in
  // an order where none stays in its place - 0, 1, 2, 9, 44, 265, 1854, ... from n = 1, 1 at
  // position 0 - by !n = n * !(n-1) + (-1)^n. !20 is the last inside the integer range: from
  // !21 the term is a float, handed back as it stands so the build ends on it. A position
  // below 0, or between two, has no term.

  function pqDerangement ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $term = 1, $i = 1; $i <= $n; $i++ ) {

      $term = $i * $term + ( $i % 2 ? -1 : 1 );

      if ( is_float ( $term ) )
        return $term;

    }

    return $term;

  }

?>
