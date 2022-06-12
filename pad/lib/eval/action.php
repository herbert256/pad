<?php
  

  function pad_eval_not ( &$result, $k, $b) {

    $result[$k][0] = ( $result [$k] [0] ) ? '' : '1';
    
    unset ($result[$b]);

  }
  
  
  function pad_eval_action ( &$result, $k, $b, $f ) {

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

      if (strpos($left, '.') === FALSE ) $left  = (int)    $left;
      else                               $left  = (double) $left;

      if (strpos($right, '.') === FALSE ) $right  = (int)    $right;
      else                                $right  = (double) $right;

      if     ( $opr == '+'   ) $now =  $left +   $right;
      elseif ( $opr == '-'   ) $now =  $left -   $right;
      elseif ( $opr == '*'   ) $now =  $left *   $right;
      elseif ( $opr == '/'   ) $now =  $left /   $right;
      elseif ( $opr == '%'   ) $now =  $left %   $right;
      else 
        throw new Exception('Unknow operation');

    }

    $result [$k] [0] = $now;
    
    unset ( $result [$b] );
    unset ( $result [$f] );

  }
  
  
?>