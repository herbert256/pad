<?php
  
  $kind = $result [$k] [2];
  $name = $result [$k] [0];
 
  if ( $result [$k] [4] ) 
    include '/pad/eval/actions/typeSingle.php';
  else
    include '/pad/eval/actions/typeParms.php';
  
  $value = include '/pad/call/any.php' ;
  
  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  padEvalOpr ($result, $myself, $start, $end );

?>