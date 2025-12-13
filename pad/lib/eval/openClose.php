<?php


  /**
   * Processes parentheses in eval expressions.
   *
   * Finds matching open/close tokens, removes them, evaluates the
   * contents between them, and links TYPE tokens to their closing paren.
   * Recurses to handle nested parentheses.
   *
   * @param array  &$result The token array (modified in place).
   * @param string $myself  Current function name for recursion.
   * @param int    $start   Start index for processing.
   * @param int    $end     End index for processing.
   *
   * @return void
   */
  function padEvalOpnCls ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $prev = $type = $open = FALSE;

    foreach ( $result as $key => $value ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $value [1] == 'open' ) {
 
        $type = $prev;
        $open = $key;
 
      } elseif ( $value [1] == 'close' ) {

        unset ( $result [$open] );
        unset ( $result [$key]  );

        if ( $type )
          $result [$type] [3] = $key;

                                                          padEvalTrace ( 'opncls9', $result );
        padEvalOpr ( $result, $myself, $open, $key );     padEvalTrace ( 'opr2', $result );
        padEvalOpnCls ( $result, $myself, $start, $end ); padEvalTrace ( 'opncls3', $result );
        return;
            
      } else

        $prev = $key;

    }

  }
   

?>