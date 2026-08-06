<?php

  // Build strategy 'bool' for the happy sequence: pqBoolHappy() keeps replacing n with the
  // sum of the squares of its digits, which always ends either at 1 - a happy number - or
  // in the cycle 4, 16, 37, 58, 89, 145, 42, 20, 4. Testing only for 1 and 4 is therefore
  // enough. The terms are 1, 7, 10, 13, 19, 23, 28, 31, 32, ...
  //
  // pqBoolHappyGo() is the digit-square-sum step itself.
  //
  // The digit-square-sum is a fixed point at 0, so a candidate below 1 would never reach
  // either end and the loop would never return. The sequence is positive, so anything
  // under 1 is outside it and is answered before the loop starts.

  function pqBoolHappy ($num, $p=0) {

    if ( $num < 1 )
      return FALSE;

    $n = $num;

    while ($n != 1 && $n != 4)
      $n = pqBoolHappyGo ($n);

    return ( $n == 1 );

  }

  function pqBoolHappyGo ($num) {

    $rem = 0;
    $sum = 0;
    while ($num > 0) {
        $rem = $num % 10;
        $sum = $sum + ($rem * $rem);
        $num = intval($num / 10);
    }

    return $sum;

  }

?>