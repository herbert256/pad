<?php

  function pqBoolHarshad ( $n, $p=0 ) {

    $sum = 0;
    for ($temp = $n; $temp > 0;
                     $temp /= 10)
        $sum += (int) $temp % 10;

    return ($n % $sum == 0);

  }

?>