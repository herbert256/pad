<?php

  // Function build for recaman: pqRecaman($n) is the nth term of Recaman's sequence, which
  // starts at 0 and at step i steps back by i when that lands on a new non-negative value
  // and forward by i otherwise - 0, 1, 3, 6, 2, 7, 13, 20, 12, 21, 11, 22, 10, ...
  //
  // The terms made so far are kept between calls, with the values they took as keys, so a
  // term is one step on from the last one kept and the "is it new" question is an isset.
  // Each term rebuilt the whole history and searched it with in_array, on the order of m^3
  // for m terms: {sequence recaman, rows=6000} took 16 seconds and sequence:recaman(6000) ran
  // into the time limit. Membership tests take the route through the precomputed table in
  // generated.php. A position between two whole ones has no term.

function pqRecaman($n)
{
  static $terms = [ 0 ], $seen = [ 0 => TRUE ];

  // A position past $padSeqMaxTries, the ceiling a run walks to, is not made: the history
  // for it outgrew the memory a request has - from=100000000 asked for gigabytes and ended
  // the request on the memory limit.

  if ( ! pqBoolWhole ( $n ) or $n > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) )
    return FALSE;

  if($n <= 1)
    return 0;

  for ( $i = count ( $terms ); $i < $n; $i++ ) {

    $curr = $terms [$i - 1] - $i;

    if ( $curr < 0 or isset ( $seen [$curr] ) )
      $curr = $terms [$i - 1] + $i;

    $terms []      = $curr;
    $seen  [$curr] = TRUE;

  }

  return $terms [$n - 1];
}

?>
