<?php

  $opr = $result [$b] [0];

  if ( in_array ( $opr, padEval_one ) ) {
    if     ( ! is_array ( $right ) ) include 'eval/actions/singleVar.php';
    elseif (   is_array ( $right ) ) include 'eval/actions/singleArr.php';
  } else {
    if     ( ! is_array ($left) and ! is_array ($right) ) include 'eval/actions/doubleVarVar.php';
    elseif (   is_array ($left) and ! is_array ($right) ) include 'eval/actions/doubleArrVar.php';
    elseif ( ! is_array ($left) and   is_array ($right) ) include 'eval/actions/doubleVarArr.php';
    elseif (   is_array ($left) and   is_array ($right) ) include 'eval/actions/doubleArrArr.php';
  }

  $result [$b] [0] = $now;
  $result [$b] [1] = 'VAL';

  padEvalOpr ( $result, $myself, $start, $end );
  
?>