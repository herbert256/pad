<?php

  function padSeqBoolHappy ($num) {

    $n = $num;
  
    while ($n != 1 && $n != 4)
      $n = padSeqBoolHappyGo ($n);
  
    return ( $n == 1 );

  }

  function padSeqBoolHappyGo ($num) {

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