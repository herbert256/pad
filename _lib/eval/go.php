<?php
  
  
  function padEvalOpenClose ( &$result, $myself ) {

    $prev = $type = $open = FALSE;

    foreach ( $result as $key => $value )

      if ( $value [1] == 'open') {
 
        $type = $prev;
        $open = $key;
 
      } elseif ( $value [1] == 'close') {

        if ( $type )
          $result [$type] [3] = $key;

        unset ( $result [$open] );
        unset ( $result [$key]  );

        padEvalOpr ( $result, $open, $key, $myself );

        return padEvalOpenClose ( $result, $myself );
            
      } else

        $prev = $key;

    padEvalType ( $result, 0, PHP_INT_MAX, $myself );
    padEvalOpr  ( $result, 0, PHP_INT_MAX, $myself );

  }


  function padEvalType ( &$result, $start, $end, $myself ) {

    $b = -1;
    
    foreach ( $result as $k => $t ) {

      if ( $k >= $start and $k <= $end and $result[$k][1] == 'TYPE' )
        return padEvalTypeGo ($k, $b, $result, $myself, $start, $end);       

      $b = $k;

    }

  }
  

  function padEvalOpr ( &$result, $start, $end, $myself ) {

    foreach ( padEval_precedence as $now ) {

      $f = $b = -1;
      
      foreach ( $result as $k => $t ) {

        if ( $b >= $start and $result[$b][0] == $now and $result[$b][1] == 'OPR' )
          if ( $k <= $end and $result[$k][1] == 'VAL' )
            if ( in_array ( $result[$b][0], ['not','!'] )  )
              return padEvalNot ($result, $k, $b, $start, $end, $myself);
            elseif ( $f >= $start and $result[$f][1] == 'VAL' )
              return padEvalAction ($result, $k, $b, $f, $start, $end, $myself);

        $f = $b;
        $b = $k;
  
      }
  
    }

  }
  

  function padEvalNot ( &$result, $k, $b, $start, $end, $myself ) {

    $result [$k] [0] = ( $result [$k] [0] ) ? '' : '1';
    
    unset ( $result [$b] );

    padEvalOpr ( $result, $start, $end, $myself );

  }
  
   
  function padEvalAction ( &$result, $k, $b, $f, $start, $end, $myself ) {

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
    else { 

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

    padEvalOpr ($result, $start, $end, $myself);


  }



  function padEvalTypeGo ( $type, $left, &$result, $myself, $start, $end ) {

    $kind = $result [$type] [2];
    $name = $result [$type] [0];

    $parm = [];
    foreach ( $result as $k => $v ) 
      if ( $k > $type and $k <= $result [$type] [3] - 1 ) {
        $parm [] = $v[0];
        unset ( $result [$k] );
      }

    $count = count ( $parm );

    if ( $left >= $start and $result [$left] [1] == 'VAL' ) {
      $value = $result [$left] [0];
      unset ($result [$left]);
    } else
      $value = $myself;
   
    $padCall = "/pad/eval/parms/$kind.php" ;
    $value   = include "/pad/call/any.php" ;
    
    $result [$type] [1] = 'VAL';
    $result [$type] [0] = padCheckValue ($value);

    padEvalType ($result, $start, $end, $myself);

  }  


?>