<?php

  // Build strategy 'bool' for the harshad (Niven) sequence: pqBoolHarshad() is TRUE when n
  // is divisible by the sum of its own digits - 1 to 10, then 12, 18, 20, 21, 24, 27, ...
  //
  // The digit loop steps down with intdiv, so it runs once per digit. Dividing by 10 in
  // floating point instead kept stepping through ever smaller fractions after the digits
  // were used up, adding zero each time until the value underflowed - the same answer for
  // roughly forty times the work.
  //
  // A candidate under 1 has no digits to add up, leaving a zero divisor, so it is answered
  // before the sum is taken - the sequence is positive, so none of those belong to it.

  function pqBoolHarshad ( $n, $p=0 ) {

    if ( $n < 1 )
      return FALSE;

    $sum = 0;

    for ( $temp = (int) $n; $temp > 0; $temp = intdiv ( $temp, 10 ) )
      $sum += $temp % 10;

    return ($n % $sum == 0);

  }

?>