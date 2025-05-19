<?php
    
  if ( $result [$k] [3] == 0 ) {
    $padEvalNextKey = padEvalNextKey ( $result, $k );
    $result [$k] [3] = ( $padEvalNextKey and $padEvalNextKey <= $end ) ? $padEvalNextKey + 1 : 0;
  }

  $parm = [];
  foreach ( $result as $key => $val ) 
    if ( $key > $k and $key <= $result [$k] [3] - 1 ) {
      $parm [] = $val[0];
      unset ( $result [$key] );
    }
   
  $count = count ( $parm );

  if ( $b >= $start and $result [$b] [1] == 'VAL' ) {
    $value = $result [$b] [0];
    unset ($result [$b]);
  } else
    $value = $myself;

  return include "eval/parms/$kind.php" ;

?>