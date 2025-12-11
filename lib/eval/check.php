<?php


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