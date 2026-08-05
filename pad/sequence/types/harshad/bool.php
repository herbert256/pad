<?php

  // Build strategy 'bool' for the harshad (Niven) sequence: pqBoolHarshad() is TRUE when n
  // is divisible by the sum of its own digits - 1 to 10, then 12, 18, 20, 21, 24, 27, ...
  //
  // The digit loop divides by 10 in floating point rather than integer, so once the digits
  // are used up it keeps stepping through ever smaller fractions, adding zero each time,
  // until the value underflows to 0. The answer is right, the loop just runs far longer
  // than the number has digits.

  function pqBoolHarshad ( $n, $p=0 ) {

    $sum = 0;
    for ($temp = $n; $temp > 0;
                     $temp /= 10)
        $sum += (int) $temp % 10;

    return ($n % $sum == 0);

  }

?>