<?php

  // Applies one operator, dispatching on whether its operands are scalars or arrays.
  //
  // Included by the eval/actions/ files once they have set $left and/or $right, with $b the key
  // of the operator token in $result. The unary operators go to singleVar / singleArr, all
  // others to the doubleVarVar / doubleArrVar / doubleVarArr / doubleArrArr combination. The
  // chosen handler leaves its outcome in $now, which replaces the operator token as a plain
  // VAL, after which padEvalOpr() is re-entered to fold the next operator in the segment.

  $opr = $result [$b] [0];

  if ( in_array ( $opr, padEval_one ) ) {
    if     ( ! is_array ( $right ) ) include PAD . 'eval/go/singleVar.php';
    elseif (   is_array ( $right ) ) include PAD . 'eval/go/singleArr.php';
  } else {
    if     ( ! is_array ($left) and ! is_array ($right) ) include PAD . 'eval/go/doubleVarVar.php';
    elseif (   is_array ($left) and ! is_array ($right) ) include PAD . 'eval/go/doubleArrVar.php';
    elseif ( ! is_array ($left) and   is_array ($right) ) include PAD . 'eval/go/doubleVarArr.php';
    elseif (   is_array ($left) and   is_array ($right) ) include PAD . 'eval/go/doubleArrArr.php';
  }

  $result [$b] [0] = $now;
  $result [$b] [1] = 'VAL'; padEvalTrace ( 'go', $result );

  padEvalOpr ( $result, $myself, $start, $end ); padEvalTrace ( 'opr5', $result );

?>