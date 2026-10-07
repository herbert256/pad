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
  // A number past the sieve is followed through the passes instead: an odd n stands at
  // position (n + 1) / 2 among the odd numbers, a pass with step k strikes it when its
  // position is a multiple of k and otherwise moves it down by the number struck before it,
  // and once its position is below the step no later pass reaches it. The steps are the
  // lucky numbers themselves, and only those up to about n / ln n are needed, so the sieve
  // is grown in doubling steps only as far as the steps run out. Sieving up to twice the
  // candidate instead cost a time that grows with its square: {sequence lucky,
  // from=1000000} sieved two million numbers and ran into the time limit.
  //
  // $pqLuckyList, $pqLuckySteps and $pqLuckyLimit are pq* globals, so inits/clear.php drops
  // them between runs and the first candidate of the next run builds them again.

  function pqBoolLucky ( $n, $p=0 ) {

    global $pqLuckyList, $pqLuckySteps, $pqLuckyLimit;

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    // The number, not the text it came as: the sieve keeps its survivors under integer keys,
    // and '07' - from='07', a request value - was looked up as the key "07" and was not lucky.

    $n = (int) $n;

    if ( ! isset ( $pqLuckyLimit ) )
      pqLuckySieve ( 100 );

    if ( $n <= $pqLuckyLimit )
      return isset ( $pqLuckyList [$n] );

    if ( $n % 2 == 0 )
      return FALSE;

    // A number past twice $padSeqMaxTries is not followed: the steps it needs outgrow any
    // sieve a request can run, and near PHP_INT_MAX n + 1 was a float intdiv() refused. A
    // listed or stored value there is named by the strict check rather than answered "not
    // lucky" for a lucky 2000029; a run that walks there - from=1999990 - has no more lucky
    // numbers to make and ends quietly, as a run past the integer range does.

    $pqLuckyMax = 2 * ( $GLOBALS ['padSeqMaxTries'] ?? 1000000 );

    if ( $n > $pqLuckyMax ) {

      if ( ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) and ( $GLOBALS ['pqPlayGiven'] ?? FALSE ) )
        padError ( "whether $n is lucky is not known: lucky numbers are followed up to $pqLuckyMax" );

      return FALSE;

    }

    $position = intdiv ( $n, 2 ) + 1;

    for ( $i = 1; ; $i++ ) {

      while ( ! isset ( $pqLuckySteps [$i] ) )
        pqLuckySieve ( 2 * $pqLuckyLimit );

      $step = $pqLuckySteps [$i];

      if ( $position < $step )
        return TRUE;

      if ( $position % $step == 0 )
        return FALSE;

      $position -= intdiv ( $position, $step );

    }

  }

  function pqLuckySieve ( $limit ) {

    global $pqLuckyList, $pqLuckySteps, $pqLuckyLimit;

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
    $pqLuckySteps = $pqLuckyNums;
    $pqLuckyLimit = $limit;

  }

?>
