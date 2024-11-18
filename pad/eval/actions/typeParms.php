<?php
  
  $parm = [];
  foreach ( $result as $key => $val ) 
    if ( $key > $k and $key <= $result [$k] [3] - 1 ) {
      $parm [] = $val[0];
      unset ( $result [$key] );
    }
   
  if ( count ( $parm ) == 1 and is_array ( $parm [0] ) )
    $parm = $parm [0];

  $count = count ( $parm );

  if ( $b >= $start and $result [$b] [1] == 'VAL' ) {
    $value = $result [$b] [0];
    unset ($result [$b]);
  } else
    $value = $myself;

  $padCall = PAD . "eval/parms/$kind.php" ;

?>