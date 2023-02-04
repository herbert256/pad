<?php
  
  function padEvalGo (&$result, $start, $end, $myself) {

go: $last = $open = FALSE;

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

          if ( $now == 'TYPE' and $result[$b][1] == 'TYPE') {

            padEvalParms ($b, $f, $result, $myself, $start, $end);

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

        }

        if ( $now == 'TYPE' and $result[$k][1] == 'TYPE' and $k == array_key_last ($result) ) {

          padEvalParms ($k, $b, $result, $myself, $start, $end);
          
          goto go;

        }

        $f = $b;
        $b = $k;
  
      }
  
    }

  }
  

?>