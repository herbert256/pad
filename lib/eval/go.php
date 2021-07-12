<?php
  
  function pad_eval_go (&$result, $start, $end, $myself) {

go: $b = -1;

    foreach ( $result as $k => $t ) {

      if ( $b >= $start and $result[$b][1] == 'VAL' and $result[$k][1] == 'VAL' 
           and in_array ( substr($result[$k][0], 0, 1), ['-', '+', '.'] ) ) {

        $result[$k-10][0] = substr($result[$k][0], 0, 1);
        $result[$k-10][1] = 'OPR';

        $result[$k][0] = substr($result[$k][0], 1);

        ksort($result);

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
          
        if ( $last and $result[$last][0] == 'TYPE' and $result[$last][1] == 'OPR' )
          $result[$last][5] = $key;

        pad_eval_go ($result, $open+1, $key-1, $myself);

        unset ($result [$open]);
        unset ($result [$key] );

        if ( $last and $result[$last][1] == '$' ) {

          $num = $now = 0;

          foreach ( $result as $cnt => $value )
            if ( $cnt > $open and $cnt < $key ) {
              $num++;
              $now = $cnt;
            }
          
          if ( $num == 1 ) {
            $result[$last][0] .= '.' . $result[$now][0];
            unset ($result [$now] );
          }

        }
        
        goto go;
            
      }

    }

    foreach ( $result as $k => $one ) {

      if ( $k < $start ) continue;
      if ( $k > $end   ) break;

      if ( $one[1] == '$' ) {

        $result[$k][1] = 'VAL';      
 
        if ( pad_field_check ( $one[0] ) ) 
          $result[$k][0] = pad_field_value ( $one[0] );
        elseif ( pad_array_check ( $one[0] ) ) {
          $result[$k][6] = 'array';
          $result[$k][7] = pad_array_value ( $one[0]);
        } else
          $result[$k][0] = $one[0]   ;

      }

    }

    foreach ( pad_eval_precedence as $now ) {

      $f = $b = -2;
      foreach ( $result as $k => $t ) {

        if ( $k > $end)
          break;

        if ( $b >= $start and $result[$b][0] == $now and $result[$b][1] == 'OPR' ) {

          if ($result[$b][0] == 'TYPE') {
        
            pad_eval_type ($b, $f, $result, $myself, $start);

            goto go;
 
           } elseif ( $result[$k][1] == 'VAL' and $result[$b][0] == 'NOT' ) {
 
            pad_eval_not ($result, $k, $b);

            goto go;
 
          } elseif ( $result[$k][1] == 'VAL' and $f >= $start and $result[$f][1] == 'VAL') {
 
            pad_eval_action ($result, $k, $b, $f);

            goto go;

          }

        }

        if ( $now == 'TYPE' and $k == array_key_last ($result) and $result[$k][0] == 'TYPE' and $result[$k][1] == 'OPR' ) {
          
          pad_eval_type ($k, $b, $result, $myself, $start);
          
          goto go;

        }

        $f = $b;
        $b = $k;
  
      }
  
    }

  }
  
?>