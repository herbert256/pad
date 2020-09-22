<?php
  

  function pad_eval_not ( &$result, $k, $b) {

    $result[$k][0] = ( $result [$k] [0] ) ? '' : '1';
    
    unset ($result[$b]);

  }
  
  
  function pad_eval_action ( &$result, $k, $b, $f ) {

    $left  = $result [$f] [0];
    $opr   = $result [$b] [0];
    $right = $result [$k] [0];

    if ( $opr == '..' ) {
      $result [$k][1] = 'VAL';
      $result [$k][6] = 'array';
      $result [$k][7] = range ( $left, $right );
      $result [$k][0] = implode ( '|', $result [$k][7] );
      unset ($result[$b]);
      unset ($result[$f]);
      return;
    }
 
    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    
    $error_level = error_reporting(E_ALL);

    try {

      if     ( $opr == 'LT'  ) $result[$k][0] = ($left <   $right) ? 1 : '';
      elseif ( $opr == 'LE'  ) $result[$k][0] = ($left <=  $right) ? 1 : '';
      elseif ( $opr == 'EQ'  ) $result[$k][0] = ($left ==  $right) ? 1 : '';
      elseif ( $opr == 'GE'  ) $result[$k][0] = ($left >=  $right) ? 1 : '';
      elseif ( $opr == 'GT'  ) $result[$k][0] = ($left >   $right) ? 1 : '';
      elseif ( $opr == 'NE'  ) $result[$k][0] = ($left <>  $right) ? 1 : '';

      elseif ( $opr == 'AND' ) $result[$k][0] = ($left AND $right) ? 1 : '';
      elseif ( $opr == 'OR'  ) $result[$k][0] = ($left OR  $right) ? 1 : '';
      elseif ( $opr == 'XOR' ) $result[$k][0] = ($left XOR $right) ? 1 : '';

      elseif ( $opr == '**'  ) $result[$k][0] = $left **  $right;
      elseif ( $opr == '+'   ) $result[$k][0] = $left +   $right;
      elseif ( $opr == '-'   ) $result[$k][0] = $left -   $right;
      elseif ( $opr == '*'   ) $result[$k][0] = $left *   $right;
      elseif ( $opr == '/'   ) $result[$k][0] = $left /   $right;
      elseif ( $opr == '%'   ) $result[$k][0] = $left %   $right;

      elseif ( $opr == '.'   ) $result[$k][0] = $left .   $right;

      else 

        throw new Exception('Unknow operation');

    }

    catch (Throwable $e) {

      $error = $e->getMessage();

      pad_error ("PAD Eval error: $error: '$left $opr $right'"); 

      $result[$k][0] = '';

    }
    
    error_reporting($error_level);
    restore_error_handler();

    $result[$k][0] = (string) $result[$k][0];
    
    unset ($result[$b]);
    unset ($result[$f]);

  }
  
  
?>