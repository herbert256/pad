<?php

  // Deals with two operators standing next to each other, which precedence alone cannot
  // resolve because the first one has no right-hand operand. The first of the pair is
  // applied "alone" - both its operands being $myself, the pipe input - and the scan
  // restarts until no adjacent pair is left. Called by padEvalOpr before it starts working
  // through the precedence list.

  function padEvalDouble ( &$result, $myself, $start, $end) {

    $previous = NULL;

    foreach ( $result as $now => $dummy ) {

      if ( $now < $start ) continue;
      if ( $now > $end   ) break;

      // A prefix operator - ! or NOT - after an operator is not a pair: it belongs to the
      // value behind it, and padEvalOpr applies it first. Taken as a pair, $one eq 2 and !
      // $zero applied the and to the pipe input and came out true.

      if ( $previous !== NULL and $result [$now] [1] == 'OPR' and $result [$previous] [1] == 'OPR'
           and ! in_array ( $result [$now] [0], padEval_one ) ) {

        $b = $previous;
        include PAD . 'eval/actions/alone.php';

        padEvalDouble ( $result, $myself, $start, $end ); padEvalTrace ( 'double3', $result );;
        return;

      }

      $previous = $now;

    }

  }

?>
