<?php

  // Function build for polite: pqPolite($n) is the nth polite number - a number that can be
  // written as a sum of two or more consecutive positive integers, which is every number
  // except 1 and the powers of two, so 3, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 17, ...
  //
  // Uses the closed form floor(n + log2(n + log2(n))), on n+1 so that term 1 comes out 3.

  function pqPolite ($n) {

    $n += 1;
    $base = 2;
    return floor ($n + (log(($n + (log($n) /
                 log($base))))) / log($base) );

  }

?>