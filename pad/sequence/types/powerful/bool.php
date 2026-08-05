<?php

  // Membership predicate for powerful: pqBoolPowerful($n) is TRUE when every prime dividing
  // n divides it at least twice - the powerful numbers 1, 4, 8, 9, 16, 25, 27, 32, 36, 49,
  // 64, 72, ...
  //
  // Divides out 2 first, then the odd factors up to sqrt(n), rejecting as soon as any
  // exponent comes out 1; whatever is left must be 1, since a surviving prime factor would
  // occur only once. The only file in the type, so it is the generation path as well and
  // the whole range is filtered through it.

  function pqBoolPowerful ($n, $p=0) {

    while ($n % 2 == 0)
    {
        $power = 0;
        while ($n % 2 == 0)
        {
            $n /= 2;
            $power++;
        }

        if ($power == 1)
        return false;
    }

    for ($factor = 3;
         $factor <= sqrt($n);
         $factor += 2)
    {

        $power = 0;
        while ($n % $factor == 0)
        {
            $n = $n / $factor;
            $power++;
        }

        if ($power == 1)
        return false;
    }

    return ($n == 1);

  }

?>