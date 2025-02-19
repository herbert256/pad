<?php
  
  
  function padEvalType ( &$result, $myself, $start, $end ) {

    $b = -1;
    
    foreach ( $result as $k => $t ) { 

      if ( $k < $start ) continue;
      if ( $k > $end   ) break;

      if ( $result[$k][1] == 'TYPE' )
        return include 'eval/type/type.php';
 
      $b = $k;

    }

  }


?>