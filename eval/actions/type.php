<?php
  
  $kind = $result [$k] [2];
  $name = $result [$k] [0];

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

        $GLOBALS ['aaeval'] [] = ["fun: $start-$end", $kind, $name, $value, $parm];
 
  $padCall = "/pad/eval/parms/$kind.php" ;
  $value   = include '/pad/call/any.php' ;
  
  $result [$k] [1] = 'VAL';
  $result [$k] [0] = $value;

  padEvalOpr ($result, $myself, $start, $end );

?>