<?php

  // Membership test for the gould sequence, 1, 2, 2, 4, 2, 4, 4, 8, ...: 2 raised to the
  // number of one bits of n - 1, so every term is a power of two, and 2^k is the term at n =
  // 2^k, n - 1 being k one bits - the powers of two from 1 up are the members. build/check.php
  // asks it for {keep gould}, {remove gould} and {flag gould}. Without it the check generated
  // the sequence as far as the candidate's position for each new largest candidate, which for
  // 2^40 is a trillion terms - the million-candidate ceiling cut it short and answered no.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolGould ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    return ( $n & ( $n - 1 ) ) == 0;

  }

?>
