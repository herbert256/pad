<?php
    
  $kind = $result [$k] [2];
  $name = $result [$k] [0];
 
  if ( $GLOBALS ['padInfo'] )
    include PAD . 'events/functions.php';

  if ( $result [$k] [4] ) 
    $value = include PAD . 'eval/type/action.php';
  else
    $value = include PAD . 'eval/type/function.php';
    
  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  unset ( $result [$k] [2] );
  unset ( $result [$k] [3] );
  unset ( $result [$k] [4] );

  padEvalTrace ( 'type9', $result );

  padEvalOpr ( $result, $myself, $start, $end ); padEvalTrace ( 'opr4', $result );

?>