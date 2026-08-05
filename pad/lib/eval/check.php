<?php

  // Handles the ranges that are too short for the normal left-operator-right shape, which
  // is how a pipe segment consisting of nothing but an operator gets evaluated. Counts the
  // tokens in $start..$end and bails out at three or more, leaving them to padEvalOpr.
  //
  // A single OPR runs eval/actions/alone.php (both operands are $myself, the pipe input);
  // OPR followed by VAL runs doubleLeft.php and VAL followed by OPR runs doubleRight.php,
  // each substituting $myself for the side that is missing. This is what lets {echo $x |
  // + 1} and {echo $x | 100 -} work. Called by padEvalOpr before the precedence walk.

  function padEvalCheck ( &$result, $myself, $start, $end ) {

    $first = $last = $count = 0;

    foreach ( $result as $now => $dummy ) {

      if ( $now < $start ) continue;
      if ( $now > $end   ) break;

      $first = $last;
      $last  = $now;

      $count++;

      if ($count > 2 )
        return;

    }

    if ( $count == 1 and $result [$last] [1] == 'OPR' ) {
      $b = $last;
      include PAD . 'eval/actions/alone.php';
    } elseif ( $count == 2 and $result [$first] [1] == 'OPR' and $result [$last] [1] == 'VAL' ) {
      $b = $first;
      $k = $last;
      include PAD . 'eval/actions/doubleLeft.php';
    } elseif ( $count == 2 and $result [$first] [1] == 'VAL' and $result [$last] [1] == 'OPR' ) {
      $b = $last;
      $f = $first;
      include PAD . 'eval/actions/doubleRight.php';
    }

  }

?>