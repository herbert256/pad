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
   

  function padEvalOpr ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    global $padEvalGo, $padEvalCnt;

    $padEvalGo [$padEvalCnt] [] = $result;

    padEvalType ( $result, $myself, $start, $end );

    foreach ( padEval_precedence as $now ) {

      $f = $b = -1;
      
      foreach ( $result as $k => $t ) { 

        if ( $k < $start ) continue;
        if ( $k > $end   ) break;

        if ( $b >= $start and $result[$b][1] == 'OPR' and $result[$b][0] == $now )
          if ( in_array ( $result[$b][0], padEval_one ) and $result[$k][1] == 'VAL' )
            return include '/pad/eval/actions/single.php';
          elseif ( in_array ( $result[$b][0], padEval_one ) and $result[$k][1] == 'OPR' )
            return include '/pad/eval/actions/singleRight.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $result[$k][1] == 'VAL' )
            return include '/pad/eval/actions/double.php';
          elseif ( ( $f == -1 or $result[$f][1] <> 'VAL' ) and $result[$k][1] == 'VAL' )
            return include '/pad/eval/actions/doubleLeft.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $result[$k][1] == 'OPR' )
            return include '/pad/eval/actions/doubleRight.php';

        $f = $b;
        $b = $k;
  
      }

    }

  }
 

  function padEvalType ( &$result, $myself, $start, $end ) {

    $b = -1;
    
    foreach ( $result as $k => $t ) { 

      if ( $k < $start ) continue;
      if ( $k > $end   ) break;

      if ( $result[$k][1] == 'TYPE' )
        return include '/pad/eval/actions/type.php';
 
      $b = $k;

    }

  }


?>