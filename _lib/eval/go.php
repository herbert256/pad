<?php
  
  
  function padEvalGo (&$result, $start, $end, $myself) {

go: $last = $open = FALSE;

    if ( $GLOBALS ['padInfo'] )
      include '/pad/info/events/eval/go.php';

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

        unset ( $result [$open] );
        unset ( $result [$key]  );
        
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

            padEvalGoParms ($b, $f, $result, $myself, $start, $end);

            goto go;

          } elseif ( $result[$b][0] == $now and $result[$b][1] == 'OPR'  ) {
 
            if ( $result[$k][1] == 'VAL' and ($result[$b][0] == 'NOT' or $result[$b][0] == '!' ) ) {
 
              padEvalGoNot ($result, $k, $b);

              goto go;
 
            } elseif ( $result[$k][1] == 'VAL' and $f >= $start and $result[$f][1] == 'VAL') {

              padEvalGoAction ($result, $k, $b, $f);

              goto go;

            }

          } 

        }

        if ( $now == 'TYPE' and $result[$k][1] == 'TYPE' and $k == array_key_last ($result) ) {

          padEvalGoParms ($k, $b, $result, $myself, $start, $end);
          
          goto go;

        }

        $f = $b;
        $b = $k;
  
      }
  
    }

  }
  

  function padEvalGoNot ( &$result, $k, $b) {

    $start = $result [$k] [0];

    $result [$k] [0] = ( $result [$k] [0] ) ? '' : '1';
    
    unset ($result[$b]);

  }
  
   
  function padEvalGoAction ( &$result, $k, $b, $f ) {

    $left  = $result [$f] [0];
    $opr   = $result [$b] [0];
    $right = $result [$k] [0];

    if     ( $opr == 'LT'  ) $now = ($left <   $right) ? 1 : '';
    elseif ( $opr == 'LE'  ) $now = ($left <=  $right) ? 1 : '';
    elseif ( $opr == 'EQ'  ) $now = ($left ==  $right) ? 1 : '';
    elseif ( $opr == 'GE'  ) $now = ($left >=  $right) ? 1 : '';
    elseif ( $opr == 'GT'  ) $now = ($left >   $right) ? 1 : '';
    elseif ( $opr == 'NE'  ) $now = ($left <>  $right) ? 1 : '';
    elseif ( $opr == 'AND' ) $now = ($left AND $right) ? 1 : '';
    elseif ( $opr == 'OR'  ) $now = ($left OR  $right) ? 1 : '';
    elseif ( $opr == 'XOR' ) $now = ($left XOR $right) ? 1 : ''; 
    elseif ( $opr == '.'   ) $now =  $left .   $right;
 
    elseif ( in_array ( $opr, ['+','-','*','/','%'] ) ) { 

      if ( strpos($left, '.' ) === FALSE ) $left  = (int)    $left;
      else                                 $left  = (double) $left;

      if ( strpos($right, '.') === FALSE ) $right = (int)    $right;
      else                                 $right = (double) $right;

      if     ( $opr == '+' ) $now = $left + $right;
      elseif ( $opr == '-' ) $now = $left - $right;
      elseif ( $opr == '*' ) $now = $left * $right;
      elseif ( $opr == '/' ) $now = $left / $right;
      elseif ( $opr == '%' ) $now = $left % $right;

    }

    $result [$k] [0] = $now ?? '';
    $result [$k] [1] = 'VAL';

    unset ( $result [$b] );
    unset ( $result [$f] );

  }
  
  
  function padEvalGoParms ($type, $left, &$result, $myself, $start, $end) {

    $kind = $result [$type] [2];
    $name = $result [$type] [0];

    $parm = [];
    foreach ( $result as $k => $v )
      if ($k <= $end and $k > $type and $k <= $result [$type] [3] - 1)
        $parm [] = $v[0];

    $count = count($parm);

    if ( $left >= $start and $result [$left] [1] == 'VAL') {
      $value = $result [$left] [0];
      unset ($result [$left]);
    } else
      $value = $myself;
   
    $padCall = "/pad/eval/parms/$kind.php" ;
    $value   = include "/pad/call/any.php" ;
    
    $result [$type] [1] = 'VAL';
    $result [$type] [0] = padCheckValue ($value);

    foreach ( $result as $key => $parm)
      if ( $key <= $end and $key > $type and $key <= $result [$type] [3] - 1 )
        unset($result[$key]);

  }


?>