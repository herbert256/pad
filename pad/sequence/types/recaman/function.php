<?php

  // Function build for recaman: pqRecaman($n) is the nth term of Recaman's sequence, which
  // starts at 0 and at step i steps back by i when that lands on a new non-negative value
  // and forward by i otherwise - 0, 1, 3, 6, 2, 7, 13, 20, 12, 21, 11, 22, 10, ...
  //
  // The terms made so far are kept between calls, with the values they took as keys, so a
  // term is one step on from the last one kept and the "is it new" question is an isset.
  // Each term rebuilt the whole history and searched it with in_array, on the order of m^3
  // for m terms: {sequence recaman, rows=6000} took 16 seconds and sequence:recaman(6000) ran
  // into the time limit. The kept terms are pqRecamanMade()'s, which bool.php asks as well. A
  // position between two whole ones has no term.

  function &pqRecamanMade () {

    static $made = [ 'terms' => [ 0 ], 'seen' => [ 0 => TRUE ] ];

    return $made;

  }

  // Makes the terms up to position $n.

  function pqRecamanUpTo ( $n ) {

    $made  = &pqRecamanMade ();
    $terms = &$made ['terms'];
    $seen  = &$made ['seen'];

    for ( $i = count ( $terms ); $i < $n; $i++ ) {

      $curr = $terms [$i - 1] - $i;

      if ( $curr < 0 or isset ( $seen [$curr] ) )
        $curr = $terms [$i - 1] + $i;

      $terms []      = $curr;
      $seen  [$curr] = TRUE;

    }

  }

function pqRecaman($n)
{

  // A position past $padSeqMaxTries, the ceiling a run walks to, is not made: the history
  // for it outgrew the memory a request has - from=100000000 asked for gigabytes and ended
  // the request on the memory limit. It answers a float past the integer range, which ends
  // the run as an overflowed term does, as pell and polite do: answered FALSE it only dropped
  // the candidate, and {sequence recaman, from=100000000, to=200000000} walked a million
  // positions that have no term here.

  if ( ! pqBoolWhole ( $n ) )
    return FALSE;

  if ( $n > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) )
    return (float) PHP_INT_MAX * 2;

  // The first term, 0, stands at position 1: a position below it has no term, where every
  // position from 0 down answered 0.

  if ( $n < 1 )
    return FALSE;

  pqRecamanUpTo ( $n );

  return pqRecamanMade () ['terms'] [$n - 1];

}

?>
