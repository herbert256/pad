<?php

  // Whether a run that grows (inits/page.php, $pqPageGrow) is made again, with twice the terms
  // (exits/grow.php): what the actions in front of page= left does not fill the page after the
  // one shown, the build made every term it was asked - it did not end on its own, out of
  // candidates or out of time, which ends the growing too - and the time before, with half the
  // terms, fewer were left. A filter that leaves no more as the run doubles - a dedup over a
  // type of a few values, an intersection with a store of a few - stops there: grown on, the
  // walk spent the whole budget of a run looking for terms there are not. While it has left
  // none at all - an onlyNow= of a store holding the first hundred primes - the run doubles
  // on up to a thousand times the terms the page after needs.
  //
  // What is left is counted up to the last term the build made, the x keys actions/assoc.php
  // gave them: a store dedup='s' merges in behind the terms counts for nothing, the terms of
  // the endless run come first.

  if ( ! ( $pqPageGrow ?? FALSE ) )
    return FALSE;

  $pqPageGrowHas = 0;
  $pqPageGrowAt  = 0;

  foreach ( $pqResult as $padK => $padV ) {
    $pqPageGrowAt++;
    if ( str_starts_with ( (string) $padK, 'x' ) )
      $pqPageGrowHas = $pqPageGrowAt;
  }

  $pqPageShort = ( $pqPageGrowHas < $pqPageGrowNeed
                   and ( $pqPageGrowHas > $pqPageGrowLast or ( ! $pqPageGrowHas and $pqRows < 1000 * $pqPageGrowNeed ) )
                   and count ( $pqResultBuilt ) >= $pqRows and ! $pqTriesOut and $pqRows <= PHP_INT_MAX / 2 );

  $pqPageGrowLast = $pqPageGrowHas;

  return $pqPageShort;

?>
