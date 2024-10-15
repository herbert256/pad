<?php
  
  
  function padEvalArray ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $GLOBALS ['aaeval'] [] = "ARRAY: $start-$end";
    $GLOBALS ['aaeval'] [] = $result;

    $open = FALSE;

    foreach ( $result as $key => $value ) {

      if ( $key < $start ) continue;
      if ( $key > $end   ) break;

      if ( $value [1] == 'a-open' ) {
 
        $open = $key;
 
      } elseif ( $value [1] == 'a-close' ) {

        unset ( $result [$open] );
        unset ( $result [$key]  );

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
            
      } 

    }

  }


  function padEvalOpnCls ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $GLOBALS ['aaeval'] [] = "OpnCls: $start-$end";
    $GLOBALS ['aaeval'] [] = $result;

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
   

  function padEvalOpr ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $GLOBALS ['aaeval'] [] = "Opr: $start-$end";
    $GLOBALS ['aaeval'] [] = $result;

    foreach ( padEval_precedence as $now ) {

      $f = $b = -1;
      
      foreach ( $result as $k => $t ) { 

        if ( $k < $start ) continue;
        if ( $k > $end   ) break;

        if ( $result[$k][1] == 'TYPE' )
          return include '/pad/eval/actions/type.php';
        elseif ( $b >= $start and $result[$b][1] == 'OPR' and $result[$b][0] == $now )
          if ( in_array ( $result[$b][0], ['not','!'] ) and $result[$k][1] == 'VAL' )
            return include '/pad/eval/actions/single.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $result[$k][1] == 'VAL' )
            return include '/pad/eval/actions/double.php';

        $f = $b;
        $b = $k;
  
      }
  
    }

  }
 

?>