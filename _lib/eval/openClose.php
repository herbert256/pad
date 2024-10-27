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

        if ( $type and $result [$type] [1] == 'TYPE' )
          $result [$type] [3] = $key;

        padEvalOpr ( $result, $myself, $open, $key );

        return padEvalOpnCls ( $result, $myself, $start, $end );
            
      } else

        $prev = $key;

    }

  }
   

?>