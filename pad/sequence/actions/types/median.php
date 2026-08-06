<?php

  // median - collapses the sequence to the median of its values: the middle one once they
  // are in order, or the mean of the two middle ones when there is an even number of them.
  //
  // The values are sorted into a copy, so the sequence's own order is what it was and only
  // the answer is affected. Picking the middle entry by position without sorting - which is
  // what this did - is the median only of a sequence that already happens to be in order,
  // and answered 5 for [9,1,5,3] where the median is 4.
  //
  // An empty sequence has no median and is left empty.

  if ( ! count ( $pqResult ) )
    return;

  $pqMedianValues = array_values ( $pqResult );

  sort ( $pqMedianValues, SORT_NUMERIC );

  $pqMedianAt = intdiv ( count ( $pqMedianValues ), 2 );

  if ( count ( $pqMedianValues ) % 2 )
    $pqMedian = $pqMedianValues [$pqMedianAt];
  else
    $pqMedian = ( $pqMedianValues [$pqMedianAt-1] + $pqMedianValues [$pqMedianAt] ) / 2;

  $pqResult = [ $pqActionKey => $pqMedian ];

?>