<?php

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