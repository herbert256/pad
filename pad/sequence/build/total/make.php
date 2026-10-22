<?php

  // Totals a range of whole numbers from its plan - first value, step, number of values, as
  // padGetRange () answers it - and leaves the answer in $pqResult as the action itself leaves
  // it, a single entry under key 0; actions/actions.php then sees $pqTotalMade and does not
  // run the action over it again. Asked for by build/total/ask.php.
  //
  // Each answer is the one the action gives over the list, type included: the terms are the
  // integers range () makes, and every total is worked out over them in the order the action
  // works it out.

  $pqTotalFirst = $pqFixed ['first'];
  $pqTotalStep  = $pqFixed ['step'];
  $pqTotalCount = min ( $pqFixed ['count'], $pqRows );
  $pqTotalLast  = $pqTotalFirst + ( $pqTotalCount - 1 ) * $pqTotalStep;
  $pqTotalDo    = $pqActions [ array_key_first ( $pqActions ) ] [0];

  $pqFixed = [];
  $pqTries = $pqTotalCount;

  // A sum is added up from 0 one term after the other, as array_sum () adds: an integer
  // while it fits, a float from the term that passes PHP_INT_MAX on. When no running total can
  // pass it - the count times the largest term stays below 9e18, a margin under PHP_INT_MAX
  // that a float compares surely - the sum is the integer the closed form gives, half the
  // count times the first and last term, worked out in an order that cannot overflow either;
  // otherwise it is added up term by term, as before. An average is that sum over the count.

  if ( $pqTotalDo == 'sum' or $pqTotalDo == 'average' ) {

    if ( $pqTotalCount == 1 )
      $pqTotal = 0 + $pqTotalFirst;
    elseif ( (float) $pqTotalCount * max ( abs ( $pqTotalFirst ), abs ( $pqTotalLast ) ) < 9.0e18 )
      $pqTotal = ( $pqTotalCount % 2 )
               ? $pqTotalCount * intdiv ( $pqTotalFirst + $pqTotalLast, 2 )
               : intdiv ( $pqTotalCount, 2 ) * ( $pqTotalFirst + $pqTotalLast );
    else
      for ( $pqTotal = 0, $pqTotalAt = 0, $pqTotalTerm = $pqTotalFirst; $pqTotalAt < $pqTotalCount; $pqTotalAt++, $pqTotalTerm += $pqTotalStep )
        $pqTotal = $pqTotal + $pqTotalTerm;

    if ( $pqTotalDo == 'average' )
      $pqTotal = $pqTotal / $pqTotalCount;

  }

  // A product is multiplied from 1 one term after the other, as array_product () does. Once
  // it is the integer 0 or NAN it stays what it is whatever an integer term does to it, and
  // the rest of the terms are left.

  elseif ( $pqTotalDo == 'product' )

    for ( $pqTotal = 1, $pqTotalAt = 0, $pqTotalTerm = $pqTotalFirst; $pqTotalAt < $pqTotalCount; $pqTotalAt++, $pqTotalTerm += $pqTotalStep ) {
      $pqTotal = $pqTotal * $pqTotalTerm;
      if ( $pqTotal === 0 or ( is_float ( $pqTotal ) and is_nan ( $pqTotal ) ) )
        break;
    }

  // The median is the middle of the terms in order - the range's terms are in order already,
  // from its lower end up by the step - or the mean of the two middle ones, as median.php
  // takes them.

  elseif ( $pqTotalDo == 'median' ) {

    $pqTotalLow = min ( $pqTotalFirst, $pqTotalLast );
    $pqTotalAt  = intdiv ( $pqTotalCount, 2 );

    $pqTotal = ( $pqTotalCount % 2 )
             ? $pqTotalLow + $pqTotalAt * abs ( $pqTotalStep )
             : ( ( $pqTotalLow + ( $pqTotalAt - 1 ) * abs ( $pqTotalStep ) ) + ( $pqTotalLow + $pqTotalAt * abs ( $pqTotalStep ) ) ) / 2;

  }

  // The terms of a range are different integers: count and distinct are the number of them.

  else
    $pqTotal = $pqTotalCount;

  $pqResult = [ 0 => $pqTotal ];

?>
