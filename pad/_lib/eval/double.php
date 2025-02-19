<?php


  function padEvalDouble ( &$result, $myself, $start, $end) {

    $previous = NULL;

    foreach ( $result as $now => $dummy ) { 

      if ( $now < $start ) continue;
      if ( $now > $end   ) break;

      if ( $previous !== NULL and $result [$now] [1] == 'OPR' and $result [$previous] [1] == 'OPR' ) {
                  
        $b = $previous;
        include 'eval/actions/alone.php';
  
        return padEvalDouble ( $result, $myself, $start, $end ); 
      
      }

      $previous = $now;

    }

  }

?>