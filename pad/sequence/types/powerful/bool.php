<?php

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
