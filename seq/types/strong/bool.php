<?php

  function padSeqBoolStrong ($number) {

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