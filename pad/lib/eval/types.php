<?php


  /**
   * Processes TYPE tokens (function/method calls).
   *
   * Finds the last TYPE token in the range and includes
   * the type processing logic from eval/type/type.php.
   *
   * @param array  &$result The token array (modified in place).
   * @param string $myself  Current function name for recursion.
   * @param int    $start   Start index for processing.
   * @param int    $end     End index for processing.
   *
   * @return void
   */
  function padEvalType ( &$result, $myself, $start=0, $end=PHP_INT_MAX  ) {

    $typeK = FALSE;
    
    $b = -1;
    
    foreach ( $result as $k => $t ) { 

      if ( $k < $start ) continue;
      if ( $k > $end   ) break;

      if ( $result[$k][1] == 'TYPE' ) {
        $typeK = $k;
        $typeB = $b;
      }
 
      $b = $k;

    }

    if ( $typeK ) {
      $k = $typeK;
      $b = $typeB;
      include PAD . 'eval/type/type.php';
    }

  }

  
?>