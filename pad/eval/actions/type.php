<?php
  
  $kind = $result [$k] [2];
  $name = $result [$k] [0];
 
  if ( $GLOBALS ['padInfo'] )
    include 'events/functions.php';

  if ( $result [$k] [4] ) 
    include 'eval/actions/typeSingle.php';
  else
    include 'eval/actions/typeParms.php';
  
  $value = include 'call/any.php' ;
  
  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  padEvalOpr ($result, $myself, $start, $end );

?>