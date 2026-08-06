<?php

  // Build strategy 'bool' for the lucky sequence: the lucky-number sieve. Start from the odd
  // numbers, then repeatedly take the next survivor k and strike out every kth of what is
  // left - 3 removes 5, 11, 17, ..., then 7 removes 19, and so on - leaving 1, 3, 7, 9, 13,
  // 15, 21, 25, 31, 33, ...
  //
  // A sieve cannot answer for one number in isolation, so pqLuckySieve() runs it once over a
  // range and pqBoolLucky() reads the answer out of that. The survivors are kept in
  // $pqLuckyList as keys, which makes the membership test a plain isset. Whether a number is
  // lucky depends only on the numbers below it, so a sieve run to any limit at or above the
  // candidate gives the right answer for it.
  //
  // The range is grown in doubling steps and only when a candidate passes the current limit,
  // so a build walking upwards sieves a handful of times rather than once per candidate.
  // $pqLuckyList and $pqLuckyLimit are pq* globals, so inits/clear.php drops them between
  // runs and the first candidate of the next run builds them again.

  function pqBoolLucky ( $n, $p=0 ) {

    global $pqLuckyList, $pqLuckyLimit;

    if ( $n < 1 )
      return FALSE;

    if ( ! isset ( $pqLuckyLimit ) or $pqLuckyLimit < $n )
      pqLuckySieve ( $n * 2 );

    return isset ( $pqLuckyList [$n] );

  }

  function pqLuckySieve ( $limit ) {

    global $pqLuckyList, $pqLuckyLimit;

    if ( $limit < 100 )
      $limit = 100;

    $pqLuckyNums = range ( 1, $limit, 2 );

    for ( $pqLuckyAt = 1; $pqLuckyAt < count ( $pqLuckyNums ); $pqLuckyAt++ ) {

      $pqLuckyStep = $pqLuckyNums [$pqLuckyAt];

      if ( $pqLuckyStep > count ( $pqLuckyNums ) )
        break;

      $pqLuckyKeep = [];

      foreach ( $pqLuckyNums as $pqLuckyKey => $pqLuckyOne )
        if ( ( $pqLuckyKey + 1 ) % $pqLuckyStep )
          $pqLuckyKeep [] = $pqLuckyOne;

      $pqLuckyNums = $pqLuckyKeep;

    }

    $pqLuckyList  = array_flip ( $pqLuckyNums );
    $pqLuckyLimit = $limit;

  }

?>