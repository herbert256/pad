<?php

  // Membership predicate and generation path for strong: pqBoolStrong($n) is TRUE when n
  // equals the sum of the factorials of its own digits - 1, 2, 145 and 40585, the numbers
  // known as strong numbers or factorions, since 145 = 1! + 4! + 5!.
  //
  // Note this is not the "strong primes" the sequence list names it after. The only file in
  // the type, so the whole range is filtered through it; generated.php holds 1, 2, 145.

  function pqBoolStrong ($number, $p=0) {

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