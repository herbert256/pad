<?php
    
  $kind = $result [$k] [2];
  $name = $result [$k] [0];
 
  if ( $GLOBALS ['padInfo'] )
    include 'events/functions.php';

  if ( $result [$k] [4] ) 
    $value = include 'eval/type/single.php';
  else
    $value = include 'eval/type/parms.php';
    
  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  unset ( $result [$k] [2] );
  unset ( $result [$k] [3] );
  unset ( $result [$k] [4] );

  padEvalTrace ( 'type9', $result );

  padEvalOpr ( $result, $myself, $start, $end ); padEvalTrace ( 'opr4', $result );

?>