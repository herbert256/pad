<?php

  // Collapses [ ... ] bracket groups into a single VAL token holding a real PHP array.
  //
  // Scans for the first a-close and pairs it with the nearest preceding a-open, so nested
  // brackets are handled innermost first. The enclosed range is evaluated in place -
  // parentheses then operators - and every value left inside becomes one array element.
  // Then it recurses to pick up the next pair, which is why it returns immediately after
  // handling one. $start/$end bound the scan when called from within another group.

  function padEvalArray ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $open = FALSE;

    foreach ( $result as $key => $value ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $value [1] == 'a-open' ) {

        $open = $key;

      } elseif ( $value [1] == 'a-close' ) {

        $result [$open] [0] = [];
        $result [$open] [1] = 'VAL';
        unset ( $result [$key]  );

                                                           padEvalTrace ( 'array3',  $result );
        padEvalOpnCls ( $result, $myself, $open+1, $key ); padEvalTrace ( 'opncls2', $result );
        padEvalOpr    ( $result, $myself, $open+1, $key ); padEvalTrace ( 'opr1',    $result );

        foreach ( $result as $key2 => $value )
          if ( $key2 > $open and $key2 < $key ) {
            $result [$open] [0] [] = $result [$key2] [0] ;
            unset ( $result [$key2] );
          }

                                                         padEvalTrace ( 'array4', $result );
        padEvalArray ( $result, $myself, $start, $end ); padEvalTrace ( 'array5', $result );
        return;

      }

    }

  }

?>