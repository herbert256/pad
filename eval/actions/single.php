<?php
  
  if     ( ! is_array ( $result [$k] [0] ) ) include '/pad/eval/actions/singleVar.php';
  elseif (   is_array ( $result [$k] [0] ) ) include '/pad/eval/actions/singleArr.php';
    
  unset ( $result [$b] );

  padEvalOpr ( $result, $myself, $start, $end );

?>