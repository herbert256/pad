<?php


  /**
   * Handles consecutive operators in eval expressions.
   *
   * When two operators appear in sequence, processes the first
   * as a standalone operation and recurses.
   *
   * @param array  &$result The token array (modified in place).
   * @param string $myself  Current function name.
   * @param int    $start   Start index for processing.
   * @param int    $end     End index for processing.
   *
   * @return void
   */
  function padEvalDouble ( &$result, $myself, $start, $end) {

    $previous = NULL;

    foreach ( $result as $now => $dummy ) { 

      if ( $now < $start ) continue;
      if ( $now > $end   ) break;

      if ( $previous !== NULL and $result [$now] [1] == 'OPR' and $result [$previous] [1] == 'OPR' ) {
                  
        $b = $previous;
        include PAD . 'eval/actions/alone.php';
  
        padEvalDouble ( $result, $myself, $start, $end ); padEvalTrace ( 'double3', $result );;
        return;
      
      }

      $previous = $now;

    }

  }

?>