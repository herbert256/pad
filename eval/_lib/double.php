<?php


  function padEvalDouble ( &$result, $myself, $start, $end) {

    $previous = NULL;

    foreach ( $result as $now => $dummy ) { 

      if ( $now < $start ) continue;
      if ( $now > $end   ) break;

      if ( $previous !== NULL and $result [$now] [1] == 'OPR' and $result [$previous] [1] == 'OPR' ) {
                  
        $b = $previous;
        include PAD . 'eval/kind/alone.php';
  
        padEvalDouble ( $result, $myself, $start, $end ); padEvalTrace ( 'double3', $result );;
        return;
      
      }

      $previous = $now;

    }

  }

?>