<?php

  // Loop build for not: the bitwise complement of each loop value, ~n, which for positive
  // n is -n-1, so 1, 2, 3, ... becomes -2, -3, -4, ...
  //
  // The complement is of a whole number, so the value is made one first and anything that
  // is no whole number has no term. Handed as it came, a from= arrived as the text the tag
  // was written with and ~ complemented its bytes - {sequence not, from=5} began with a
  // byte of garbage - and a fraction or a word in a make play did the same or ended the
  // request on PHP's deprecation.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  return ~ (int) $pqLoop;

?>
