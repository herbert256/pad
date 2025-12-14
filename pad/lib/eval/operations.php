<?php


  /**
   * Applies operators by precedence order.
   *
   * Processes double operators, edge cases, and type calls first.
   * Then iterates through operators in precedence order, dispatching
   * to appropriate action files (single, double, left, right).
   *
   * @param array  &$result The token array (modified in place).
   * @param string $myself  Current function name for recursion.
   * @param int    $start   Start index for processing.
   * @param int    $end     End index for processing.
   *
   * @return mixed Result from included action file.
   */
  function padEvalOpr ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    padEvalDouble ( $result, $myself, $start, $end ); padEvalTrace ( 'double2', $result );
    padEvalCheck  ( $result, $myself, $start, $end ); padEvalTrace ( 'check2', $result );
    padEvalType   ( $result, $myself, $start, $end ); padEvalTrace ( 'type2', $result );

    foreach ( padEval_precedence as $now ) {

      $f = $b = -1;

      foreach ( $result as $k => $t ) {

        if ( $k < $start ) continue;
        if ( $k > $end   ) break;

        if ( $b >= $start and $result[$b][1] == 'OPR' and $result[$b][0] == $now )
          if     ( in_array ( $result[$b][0], padEval_one ) and $t[1] == 'VAL' ) return include PAD . 'eval/actions/single.php';
          elseif ( in_array ( $result[$b][0], padEval_one ) and $t[1] == 'OPR' ) return include PAD . 'eval/actions/singleRight.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $t[1] == 'VAL' ) return include PAD . 'eval/actions/double.php';
          elseif ( ( $f == -1 or $result[$f][1] <> 'VAL' )  and $t[1] == 'VAL' ) return include PAD . 'eval/actions/doubleLeft.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $t[1] == 'OPR' ) return include PAD . 'eval/actions/doubleRight.php';

        $f = $b;
        $b = $k;

      }

    }

  }


?>