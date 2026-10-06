<?php

  // The operator engine: applies one operator to the token range $start..$end and lets the
  // action file recurse back in, so the range is reduced a step at a time until no operator
  // matches any more.
  //
  // First the preparatory passes - padEvalDouble for operators standing side by side,
  // padEvalCheck for a range too short to have two operands, padEvalType to turn a typed
  // reference or function call into a value. Then padEval_groups is walked strongest first,
  // and the token stream scanned for the first operator of that group - the last one for
  // the right-binding ** ; $f is the left operand candidate, $b the operator and $t (at
  // key $k) the right one.
  //
  // Which of eval/actions/ is included depends on what is actually present: single and
  // singleRight for the unary NOT forms, double when both sides are values, doubleLeft and
  // doubleRight when a side is missing - those substitute $myself, the pipe input, which is
  // what makes {echo $x | + 1} add to the piped value. Each action is entered with
  // `return include`, so exactly one operator is applied per call.

  function padEvalOpr ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    if ( padEvalTernary ( $result, $myself, $start, $end ) )
      return;

    padEvalDouble ( $result, $myself, $start, $end ); padEvalTrace ( 'double2', $result );
    padEvalCheck  ( $result, $myself, $start, $end ); padEvalTrace ( 'check2', $result );
    padEvalType   ( $result, $myself, $start, $end ); padEvalTrace ( 'type2', $result );

    foreach ( padEval_groups as $group ) {

      $keys = [];

      foreach ( $result as $k => $t ) {
        if ( $k < $start ) continue;
        if ( $k > $end   ) break;
        $keys [] = $k;
      }

      $order = array_keys ( $keys );

      if ( $group === [ '**' ] )
        $order = array_reverse ( $order );

      foreach ( $order as $j ) {

        if ( $j < 1 )
          continue;

        $k = $keys [$j];
        $t = $result [$k];
        $b = $keys [$j-1];
        $f = ( $j > 1 ) ? $keys [$j-2] : -1;

        if ( $result[$b][1] == 'OPR' and in_array ( $result[$b][0], $group, TRUE ) )
          if     ( in_array ( $result[$b][0], padEval_one ) and $t[1] == 'VAL' ) return include PAD . 'eval/actions/single.php';
          elseif ( in_array ( $result[$b][0], padEval_one ) and $t[1] == 'OPR' ) return include PAD . 'eval/actions/singleRight.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $t[1] == 'VAL' ) return include PAD . 'eval/actions/double.php';
          elseif ( ( $f == -1 or $result[$f][1] != 'VAL' )  and $t[1] == 'VAL' ) return include PAD . 'eval/actions/doubleLeft.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $t[1] == 'OPR' ) return include PAD . 'eval/actions/doubleRight.php';

      }

    }

  }

?>
