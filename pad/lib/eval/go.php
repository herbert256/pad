<?php
  
  function padEvalGo (&$result, $start, $end, $myself) {

go: padEvalTrace  ('go', ['start' => $start, 'end' => $end, 'go' => $result] );

    $GLOBALS ['padTrace_now'] = [];

    if  ( count($result) > 1 ) {
 
      $f = reset($result);
      $s = next($result);
 
      if ( 
           ( ( $f [1] == 'VAL' and ! $f [0] ) or ( isset ( $f [4] ) and ! count ( $f [4] ) ) )
           and $s [0] == 'AND' and $s [1] == 'OPR'
         ) {

        if ( $GLOBALS ['padTrace'] ) 
          padEvalTrace  ('fast-and', [ 'first' => $f, 'second' => $s ] );

        $result = [ 100 => ['0' => '', '1'=> 'VAL' ] ];
        return;

      }
 
      if ( 
           ( ( $f [1] == 'VAL' and $f [0] ) or ( isset ( $f [4] ) and count ( $f [4] ) ) )
           and $s [0] == 'OR' and $s [1] == 'OPR'
         ) {

        if ( $GLOBALS ['padTrace'] ) 
          padEvalTrace  ('fast-or', [ 'first' => $f, 'second' => $s ] );

        $result = [ 100 => ['0' => 1, '1'=> 'VAL' ] ];
        return;

      }
 
    }

    $b = -1;

    foreach ( $result as $k => $t ) {

      if ( $b >= $start and $result[$b][1] == 'VAL' and $result[$k][1] == 'VAL' 
           and in_array ( substr($result[$k][0], 0, 1), ['-', '+', '.'] ) ) {

        $result[$k-10][0] = substr($result[$k][0], 0, 1);
        $result[$k-10][1] = 'OPR';

        $result[$k][0] = substr($result[$k][0], 1);

        goto go;

      }

      if ( $b >= $start )
        break;

      $b=$k;

    }

    $last = $open = FALSE;

    foreach ($result as $key => $value) {

      if ( $key >= $start and $value[1] == 'open')
        $open = $key;

      if ( $key <= $end and $value[1] == 'close') {

        foreach ($result as $key2 => $value2)
          if ($key2<$open)
            $last = $key2;
          
        if ( $last and $result[$last][1] == 'TYPE' )
          $result[$last][3] = $key;

        padEvalGo ($result, $open+1, $key-1, $myself);

        unset ($result [$open]);
        unset ($result [$key] );
        
        goto go;
            
      }

    }

    foreach ( padEval_precedence as $now ) {

      $f = $b = -2;
      foreach ( $result as $k => $t ) {

        if ( $k > $end)
          break;
       
        if ( $b >= $start ) {

          $GLOBALS ['padTrace_now'] = $result[$b];

          if ( $now == 'TYPE' and $result[$b][1] == 'TYPE') {

            padEvalType ($b, $f, $result, $myself, $start, $end);

            goto go;

          } elseif ( $result[$b][0] == $now and $result[$b][1] == 'OPR'  ) {
 
            if ( $result[$k][1] == 'VAL' and ($result[$b][0] == 'NOT' or $result[$b][0] == '!' ) ) {
 
              padEvalNot ($result, $k, $b);

              goto go;
 
            } elseif ( $result[$k][1] == 'VAL' and $f >= $start and $result[$f][1] == 'VAL') {

              padEvalAction ($result, $k, $b, $f);

              goto go;

            }

          } 

         $GLOBALS ['padTrace_now'] = [];

        }

        if ( $now == 'TYPE' and $k == array_key_last ($result) and $result[$k][1] == 'TYPE' ) {

          $GLOBALS ['padTrace_now'] = $result[$k];
          
          padEvalType ($k, $b, $result, $myself, $start, $end);
          
          goto go;

        }

        $f = $b;
        $b = $k;
  
      }
  
    }

  }
  

  function array_key_next ($array, $key) {

    foreach ($array as $k =>$v )
      if ( $k > $key )
        return $k;

    return FALSE;

  }

?>