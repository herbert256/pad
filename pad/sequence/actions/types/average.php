<?php

  // average - collapses the sequence to the mean of its values, as a single entry under
  // $pqActionKey. There is no mean of nothing, so an empty sequence is left empty rather
  // than divided by its own count.

  if ( ! count ( $pqResult ) )
    return;

  // The mean of the numbers among the values: a word - a list or a store can hold one - ended
  // the request on PHP's "Addition is not supported on type string". Values with no number
  // among them have no mean either, and leave nothing.

  $pqAverageOf = array_filter ( $pqResult, 'is_numeric' );

  $pqResult = count ( $pqAverageOf )
            ? [ $pqActionKey => array_sum ( $pqAverageOf ) / count ( $pqAverageOf ) ]
            : [];

?>
