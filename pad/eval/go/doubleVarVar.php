<?php

  // The scalar OP scalar core of the evaluator: computes $now from $left, $opr and $right.
  //
  // Comparison and logical operators yield PAD's string booleans (1 for true, '' for false)
  // and '.' concatenates. Everything else is arithmetic, for which both operands are first
  // cast to int, or to float when they contain a '.', so that '007' + 1 behaves as a number.
  // The three array variants in this directory all reduce to this file once they have
  // unwrapped their operands.

  if     ( $opr == 'LT'  ) $now = ($left <   $right) ? 1 : '';
  elseif ( $opr == 'LE'  ) $now = ($left <=  $right) ? 1 : '';
  elseif ( $opr == 'EQ'  ) $now = ($left ==  $right) ? 1 : '';
  elseif ( $opr == 'GE'  ) $now = ($left >=  $right) ? 1 : '';
  elseif ( $opr == 'GT'  ) $now = ($left >   $right) ? 1 : '';
  elseif ( $opr == 'NE'  ) $now = ($left !=  $right) ? 1 : '';
  elseif ( $opr == 'AND' ) $now = ($left AND $right) ? 1 : '';
  elseif ( $opr == 'OR'  ) $now = ($left OR  $right) ? 1 : '';
  elseif ( $opr == 'XOR' ) $now = ($left XOR $right) ? 1 : '';
  elseif ( $opr == '.'   ) $now =  $left .   $right;
  else {

    if ( strpos($left, '.' ) === FALSE ) $left  = (int)   $left;
    else                                 $left  = (float) $left;

    if ( strpos($right, '.') === FALSE ) $right = (int)   $right;
    else                                 $right = (float) $right;

    if     ( $opr == '+' ) $now = $left + $right;
    elseif ( $opr == '-' ) $now = $left - $right;
    elseif ( $opr == '*' ) $now = $left * $right;
    elseif ( $opr == '/' ) $now = $left / $right;
    elseif ( $opr == '%' ) $now = $left % $right;

  }

?>