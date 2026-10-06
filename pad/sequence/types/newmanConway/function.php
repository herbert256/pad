<?php

  // Function build for newmanConway: pqNewmanConway($n) is the nth term of the
  // Newman-Conway sequence, a(1) = a(2) = 1 and a(n) = a(a(n-1)) + a(n - a(n-1)), giving
  // 1, 1, 2, 2, 3, 4, 4, 4, 5, 6, 7, 7, 8, ...
  //
  // Every term reads only terms before it, so the terms are made in order from the last one
  // kept, and kept between calls. Asked by recursion with a memo, a far position went down
  // one call per term not yet made: from=60000 ended the request on PHP's maximum call stack
  // size. A position past $padSeqMaxTries, the ceiling a run walks to, is not made - the
  // list for it would outgrow the memory a request has.
  //
  // The sequence starts at a(1): a position below it, or between two, has no term and
  // answers FALSE, which drops the candidate - a(0) asked for a(a(-1)) down to the end of
  // the stack.

function pqNewmanConway ($n) {

  static $terms = [ 1 => 1, 2 => 1 ];

  if ( ! pqBoolWhole ( $n ) or $n < 1 or $n > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) )
    return FALSE;

  for ( $i = count ( $terms ) + 1; $i <= $n; $i++ )
    $terms [$i] = $terms [ $terms [$i - 1] ] + $terms [ $i - $terms [$i - 1] ];

  return $terms [$n];
}

?>
