<?php

  // The operator engine: applies one operator to the token range $start..$end and lets the
  // action file recurse back in, so the range is reduced a step at a time until no operator
  // matches any more.
  //
  // First the preparatory passes - padEvalDouble for operators standing side by side,
  // padEvalCheck for a range too short to have two operands, padEvalType to turn a typed
  // reference or function call into a value. Then padEval_groups is walked strongest first,
  // and the token stream scanned for the first operator of that group - the last one for
  // the right-binding ** and for the unary groups, so the operator nearest its operand is
  // applied first: in ! ! 0 and ! -$n the first ! met an operator on its right and negated
  // the piped value instead, and !!0 printed 11; $f is the left operand candidate, $b the
  // operator and $t (at key $k) the right one.
  //
  // Which of eval/actions/ is included depends on what is actually present: single and
  // singleRight for the unary forms, double when both sides are values, doubleLeft and
  // doubleRight when a side is missing - those substitute $myself, the pipe input, which is
  // what makes {echo $x | + 1} add to the piped value. Each action is entered with
  // `return include`, so exactly one operator is applied per call.

  function padEvalOpr ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    // Each preparatory pass runs only when the range holds what it works on (padEvalOprScan)
    // - most ranges hold no ?, no two operators side by side and no call - and the range is
    // read again only after a pass that changed it. Each pass read the whole range itself.

    $scan = padEvalOprScan ( $result, $start, $end );

    if ( $scan ['?'] ) {
      if ( padEvalTernary ( $result, $myself, $start, $end ) )
        return;
      $scan = padEvalOprScan ( $result, $start, $end );
    }

    if ( $scan ['pair'] ) {
      padEvalDouble ( $result, $myself, $start, $end ); padEvalTrace ( 'double2', $result );
      $scan = padEvalOprScan ( $result, $start, $end );
    }

    if ( $scan ['opr'] and $scan ['count'] <= 2 ) {
      padEvalCheck ( $result, $myself, $start, $end ); padEvalTrace ( 'check2', $result );
    }

    if ( $scan ['type'] ) {
      padEvalType ( $result, $myself, $start, $end ); padEvalTrace ( 'type2', $result );
    }

    // The range's keys and the operators in it, read once: a group none of whose operators
    // stands in the range has nothing to apply, and the keys do not change between groups -
    // an operator applied returns at once. They were read again for each of the twelve
    // groups, on every call, and a call is made for every operator applied.

    $keys = [];
    $oprs = [];

    foreach ( $result as $k => $t ) {
      if ( $k < $start ) continue;
      if ( $k > $end   ) break;
      $keys [] = $k;
      if ( $t [1] == 'OPR' )
        $oprs [ $t [0] ] = TRUE;
    }

    if ( ! $oprs )
      return;

    foreach ( padEval_groups as $group ) {

      $present = FALSE;

      foreach ( $group as $opr )
        if ( isset ( $oprs [$opr] ) ) {
          $present = TRUE;
          break;
        }

      if ( ! $present )
        continue;

      $order = array_keys ( $keys );

      if ( in_array ( $group, [ [ '**' ], [ '!', 'NEG', 'POS' ], [ 'NOT' ] ], TRUE ) )
        $order = array_reverse ( $order );

      foreach ( $order as $j ) {

        if ( $j < 1 )
          continue;

        $k = $keys [$j];
        $t = $result [$k];
        $b = $keys [$j-1];
        $f = ( $j > 1 ) ? $keys [$j-2] : -1;

        // The right operand of ** may open with a sign or a !, which binds weaker than the
        // power and so would still stand there: 2 ** -$b took the piped value as its
        // exponent. That operand's own unary operators are applied first, the innermost
        // one now and the others as padEvalOpr comes back round.

        if ( $group === [ '**' ] and $result[$b][1] == 'OPR' and $result[$b][0] === '**'
             and $t[1] == 'OPR' and in_array ( $t[0], padEval_one ) ) {

          for ( $u = $j; isset ( $keys [$u+1] ) and $result [ $keys [$u+1] ] [1] == 'OPR'
                         and in_array ( $result [ $keys [$u+1] ] [0], padEval_one ); $u++ ) ;

          if ( isset ( $keys [$u+1] ) and $result [ $keys [$u+1] ] [1] == 'VAL' ) {
            $b = $keys [$u];
            $k = $keys [$u+1];
            return include PAD . 'eval/actions/single.php';
          }

        }

        if ( $result[$b][1] == 'OPR' and in_array ( $result[$b][0], $group, TRUE ) )
          if     ( in_array ( $result[$b][0], padEval_one ) and $t[1] == 'VAL' ) return include PAD . 'eval/actions/single.php';
          elseif ( in_array ( $result[$b][0], padEval_one ) and $t[1] == 'OPR' ) return include PAD . 'eval/actions/singleRight.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $t[1] == 'VAL' ) return include PAD . 'eval/actions/double.php';
          elseif ( ( $f == -1 or $result[$f][1] != 'VAL' )  and $t[1] == 'VAL' ) return include PAD . 'eval/actions/doubleLeft.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $t[1] == 'OPR' ) return include PAD . 'eval/actions/doubleRight.php';

      }

    }

  }

  // What the preparatory passes of padEvalOpr look for in the range: the tokens counted,
  // whether an operator is there, a ? (padEvalTernary), two operators side by side the second
  // of which is no prefix (padEvalDouble's own test), a typed reference or call (padEvalType).

  function padEvalOprScan ( $result, $start, $end ) {

    $scan = [ 'count' => 0, 'opr' => FALSE, '?' => FALSE, 'pair' => FALSE, 'type' => FALSE ];
    $prev = FALSE;

    foreach ( $result as $k => $t ) {

      if ( $k < $start ) continue;
      if ( $k > $end   ) break;

      $scan ['count']++;

      $opr = ( $t [1] == 'OPR' );

      if ( $opr ) {
        $scan ['opr'] = TRUE;
        if ( $t [0] === '?' )
          $scan ['?'] = TRUE;
        if ( $prev and ! in_array ( $t [0], padEval_one ) )
          $scan ['pair'] = TRUE;
      } elseif ( $t [1] == 'TYPE' )
        $scan ['type'] = TRUE;

      $prev = $opr;

    }

    return $scan;

  }

?>
