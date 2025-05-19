<?php
  
  
  function padEvalArray ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $prev = $type = $open = FALSE;

    foreach ( $result as $key => $value ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $value [1] == 'a-open' ) {
 
        $type = $prev;
        $open = $key;
 
      } elseif ( $value [1] == 'a-close' ) {

        unset ( $result [$open] );
        unset ( $result [$key]  );

        if ( $type )
          $result [$type] [3] = $key;
   
                                                         padEvalTrace ( 'array9', $result );
        padEvalOpnCls ( $result, $myself, $open, $key ); padEvalTrace ( 'opncls2', $result );
        padEvalOpr    ( $result, $myself, $open, $key ); padEvalTrace ( 'opr1', $result );

        $result [$open] [0] = [];
        $result [$open] [1] = 'VAL';

        foreach ( $result as $key2 => $value )
          if ( $key2 > $open and $key2 < $key ) {
            $result [$open] [0] [] = $result [$key2] [0] ;
            unset ( $result [$key2] );
          }

        padEvalArray ( $result, $myself, $start, $end ); padEvalTrace ( 'array2', $result );;
        return;
            
      } else

        $prev = $key;

    }

  }


?>