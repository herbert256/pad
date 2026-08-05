<?php

  // Membership predicate and generation path for semiprime: pqBoolSemiprime($num) is TRUE
  // when num is the product of exactly two primes, the two allowed to be equal - 4, 6, 9,
  // 10, 14, 15, 21, 22, 25, 26, ...
  //
  // Counts prime factors with multiplicity by trial division, giving up as soon as three
  // are found. Only factors up to sqrt(num) are looked for; whatever is left at the end is
  // prime and counts as one more. The only file in the type, so it is the generation path
  // as well and the whole range is filtered through it.

  function pqBoolSemiprime ($num, $p=0) {

    $cnt = 0;

    for ( $i = 2; $cnt < 2 &&
          $i * $i <= $num; ++$i)
        while ($num % $i == 0) {
            $num /= $i;
            ++$cnt;
        }

    if ($num > 1)
        ++$cnt;

    return $cnt == 2;

  }

?>