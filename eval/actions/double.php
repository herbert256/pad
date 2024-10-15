<?php
  
  $left  = $result [$f] [0];
  $opr   = $result [$b] [0];
  $right = $result [$k] [0];
  $now   = '';

  if     ( ! is_array ($left) and ! is_array ($right) ) include '/pad/eval/actions/doubleVarVar.php';
  elseif (   is_array ($left) and ! is_array ($right) ) include '/pad/eval/actions/doubleArrVar.php';
  elseif ( ! is_array ($left) and   is_array ($right) ) include '/pad/eval/actions/doubleVarArr.php';
  elseif (   is_array ($left) and   is_array ($right) ) include '/pad/eval/actions/doubleArrArr.php';

  $result [$k] [0] = $now;
  $result [$k] [1] = 'VAL';

  unset ( $result [$b] );
  unset ( $result [$f] );

  padEvalOpr ( $result, $myself, $start, $end );

?>