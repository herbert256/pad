<?php

  // Collapses ( ... ) groups, the same shape as padEvalArray: find the first close, pair it
  // with the nearest preceding open, drop both bracket tokens, evaluate the enclosed range
  // with padEvalOpr, then recurse for the next pair and return.
  //
  // The token immediately before the open is remembered as well: if it is a TYPE token the
  // close position is stored in its [3], marking where that function call's parameter list
  // ends so eval/type/parms.php knows how far to read.

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