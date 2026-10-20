<?php

  // Function build for vanEck: pqVanEck($n) is the nth term of Van Eck's sequence, which
  // starts at 0 and after a term t answers how many steps back t was seen before, or 0 when
  // it is new - 0, 0, 1, 0, 2, 0, 2, 2, 1, 6, 0, 5, ... from n = 1.
  //
  // The terms made are kept between calls with, for every value, the position it was last
  // seen at, so a term is one step on from the last one kept and the look back an isset - as
  // recaman/function.php keeps its terms. The kept terms are pqVanEckMade()'s, which bool.php
  // asks as well. A position past $padSeqMaxTries, the ceiling a run walks to, is not made: it
  // answers a float past the integer range, which ends the run, as recaman does. A position
  // below 1, or between two, has no term.

  function &pqVanEckMade () {

    static $made = [ 'terms' => [ 1 => 0 ], 'last' => [] ];

    return $made;

  }

  // Makes the terms up to position $n. 'last' holds the position every value was last seen at
  // before the newest term, which is not in it yet.

  function pqVanEckUpTo ( $n ) {

    $made  = &pqVanEckMade ();
    $terms = &$made ['terms'];
    $last  = &$made ['last'];

    for ( $i = count ( $terms ); $i < $n; $i++ ) {
      $term           = $terms [$i];
      $terms [$i + 1] = isset ( $last [$term] ) ? $i - $last [$term] : 0;
      $last  [$term]  = $i;
    }

  }

  function pqVanEck ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    if ( $n > ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 ) )
      return (float) PHP_INT_MAX * 2;

    pqVanEckUpTo ( (int) $n );

    return pqVanEckMade () ['terms'] [(int) $n];

  }

?>
