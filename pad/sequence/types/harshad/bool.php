<?php

  function padSeqBoolHarshad ( $n ) {

    // calculate sum of digits
    $sum = 0;
    for ($temp = $n; $temp > 0;
                     $temp /= 10)
        $sum += (int) $temp % 10;
 
    // Return true if sum of
    // digits is multiple of n
    return ($n % $sum == 0);

  }

?>