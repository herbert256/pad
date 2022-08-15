<?php
  

  function padEvalNot ( &$result, $k, $b) {

    $start = $result [$k] [0];

    $result [$k] [0] = ( $result [$k] [0] ) ? '' : '1';
    
    padEvalTrace ('not', [ 'start' => $start, 'result' => $result [$k] [0] ] );

    unset ($result[$b]);

  }
  
  
  function padEvalActionArray ( $left, $opr, $right ) {

    $action = strtolower ($opr);

    if     ( $action == '+' ) $action = 'append';
    elseif ( $action == '.' ) $action = 'append';
    elseif ( $action == '-' ) $action = 'minus';
    elseif ( $action == '*' ) $action = 'multiple';
    elseif ( $action == '/' ) $action = 'divide';
    elseif ( $action == '%' ) $action = 'mod';

    if ( ! file_exists(PAD."sequence/eval/$action.php") ) {
      padError ("Unsupported array operator: $opr");      
      $now = '';
    }
    else 
      $now = include PAD."sequence/eval/$action.php";

    padEvalTrace  ('array', [ 'left' => $left, 'action' => $action, 'right' => $right, 'result' => $now ] );

    return $now;

  }
  
  function padEvalAction ( &$result, $k, $b, $f ) {

    $left  = $result [$f] [0];
    $opr   = $result [$b] [0];
    $right = $result [$k] [0];

    if ( isset ( $result [$f] [4] ) )
      
      $now = padEvalActionArray ($result [$f] [4], $result [$b] [0], $result [$k] [4] ?? [] );
 
    else {

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

        if (strpos($left, '.') === FALSE ) $left  = (int)    $left;
        else                               $left  = (double) $left;

        if (strpos($right, '.') === FALSE ) $right  = (int)    $right;
        else                                $right  = (double) $right;

        if     ( $opr == '+'   ) $now =  $left + $right;
        elseif ( $opr == '-'   ) $now =  $left - $right;
        elseif ( $opr == '*'   ) $now =  $left * $right;
        elseif ( $opr == '/'   ) $now =  $left / $right;
        elseif ( $opr == '%'   ) $now =  $left % $right;
        else 
          throw new Exception('Unknow operation');

      }

    }

    $result [$k] [0] = $now;
    $result [$k] [1] = 'VAL';

    if ( is_array($now)) {
      $result [$k] [0] = '*ARRAY*';
      $result [$k] [4] = $now;
    } elseif ( isset($result [$k] [4]))
      unset ( $result [$k] [4] );

    unset ( $result [$b] );
    unset ( $result [$f] );

    padEvalTrace  ('action', [ 'left' => $left, 'operation' => $opr, 'right' => $right, 'result' => $now ] );

  }
  
  
?>  