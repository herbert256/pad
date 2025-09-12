<?php

  function pqBoolHappy ($num, $p=0) {

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