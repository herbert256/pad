<?php

  // minimum, minimum=N - reduces the sequence to its N smallest values, one when no count
  // is given; maximum.php delegates here for the N largest. Sorts by value to pick them,
  // keeping keys, then goes through actions/order/order.php so the survivors come back
  // out in the sequence's original order rather than sorted.

  // A count past the length keeps all of it, as first= and last= do: past the integer range
  // the count reached array_slice () as the text it was and ended the request on its TypeError.

  $pqActionCnt = (int) min ( (float) $pqActionCnt, count ( $pqResult ) );

  asort ( $pqResult );

  if ( $pqAction == 'minimum' )
    $pqResult = array_slice ( $pqResult, 0,                 $pqActionCnt, true );
  else
    $pqResult = array_slice ( $pqResult, $pqActionCnt * -1, $pqActionCnt, true );

  $pqResult = include PQ . 'actions/order/order.php';

?>
