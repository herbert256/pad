<?php

  $opr = $result [$b] [0];

  if ( in_array ( $opr, padEval_one ) ) {
    if     ( ! is_array ( $right ) ) include 'eval/go/singleVar.php';
    elseif (   is_array ( $right ) ) include 'eval/go/singleArr.php';
  } else {
    if     ( ! is_array ($left) and ! is_array ($right) ) include 'eval/go/doubleVarVar.php';
    elseif (   is_array ($left) and ! is_array ($right) ) include 'eval/go/doubleArrVar.php';
    elseif ( ! is_array ($left) and   is_array ($right) ) include 'eval/go/doubleVarArr.php';
    elseif (   is_array ($left) and   is_array ($right) ) include 'eval/go/doubleArrArr.php';
  }

  $result [$b] [0] = $now;
  $result [$b] [1] = 'VAL'; padEvalTrace ( 'go', $result );

  padEvalOpr ( $result, $myself, $start, $end ); padEvalTrace ( 'opr5', $result );
  
?>