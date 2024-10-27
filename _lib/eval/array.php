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

        if ( $type and $result [$type] [1] == 'TYPE' )
          $result [$type] [3] = $key;
        
        padEvalOpnCls ( $result, $myself, $open, $key );
        padEvalOpr    ( $result, $myself, $open, $key );

        $result [$open] [0] = [];
        $result [$open] [1] = 'VAL';

        foreach ( $result as $key2 => $value )
          if ( $key2 > $open and $key2 < $key ) {
            $result [$open] [0] [] = $result [$key2] [0] ;
            unset ( $result [$key2] );
          }

        return padEvalArray ( $result, $myself, $start, $end );
            
      } else

        $prev = $key;

    }

  }


?>