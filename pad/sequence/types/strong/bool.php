<?php

  // Membership predicate and generation path for strong: pqBoolStrong($n) is TRUE when n
  // equals the sum of the factorials of its own digits - 1, 2, 145 and 40585, the numbers
  // known as strong numbers or factorions, since 145 = 1! + 4! + 5!.
  //
  // Note these are the strong numbers, not the strong primes - a different sequence that
  // shares the name. The only file in the type, so the whole range is filtered through it;
  // generated.php holds all four, the last of them beyond the default try limit.
  //
  // An empty digit loop leaves a sum of 0, which matched a candidate of 0 and reported it a
  // member; the factorions are positive, so anything under 1 is answered first.

  function pqBoolStrong ($number, $p=0) {

    if ( $number < 1 )
      return FALSE;

    $x = $number;
    $sum = 0;

    while ($number != 0) {
      $fact = 1;
      for($i = 1; $i <= $number % 10; $i++)
        $fact *= $i;
      $sum += $fact;
      $number = (int)($number / 10);
    }

    return $sum == $x;

  }

?>