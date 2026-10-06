<?php

  // The scalar OP scalar core of the evaluator: computes $now from $left, $opr and $right.
  //
  // Comparison and logical operators yield PAD's string booleans (1 for true, '' for false)
  // and '.' concatenates. Everything else is arithmetic, for which both operands are first
  // cast to int, or to float when they contain a '.', so that '007' + 1 behaves as a number.
  // The three array variants in this directory all reduce to this file once they have
  // unwrapped or summed their operands.
  //
  // ** is one of them. The tokeniser has always recognised it and padEval_precedence has always
  // ranked it, but no line here computed it, so 2 ** 3 left $now unset and rendered the operator
  // itself rather than 8.

  // A boolean compares as PAD writes it, TRUE as 1 and FALSE as empty, and NULL as empty:
  // the bare word true is PHP's TRUE, and 'no' eq true was true because 'no' == TRUE is.

  if ( in_array ( $opr, [ 'LT', 'LE', 'EQ', 'GE', 'GT', 'NE' ] ) ) {
    if ( is_bool ( $left  ) or $left  === NULL ) $left  = $left  ? '1' : '';
    if ( is_bool ( $right ) or $right === NULL ) $right = $right ? '1' : '';
  }

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

    // An operand is read as the number it spells - 1e-3 is a thousandth, where the int cast
    // made it 0 - with empty and NULL as 0 (padEvalNumber). Text that is no number is a
    // strict-mode error, 'abc' + 1 was quietly 1, and 0 in the lenient walk; a division or
    // modulo by zero likewise, where it was an uncaught DivisionByZeroError.

    $left  = padEvalNumber ( $left,  $opr );
    $right = padEvalNumber ( $right, $opr );

    if ( in_array ( $opr, [ '/', '%' ] ) and $right == 0 ) {

      global $padCheckSyntax;

      if ( $padCheckSyntax )
        padError ( "a division by zero in $opr" );

      $now = '';

    }
    elseif ( $opr == '**') $now = $left ** $right;
    elseif ( $opr == '+' ) $now = $left + $right;
    elseif ( $opr == '-' ) $now = $left - $right;
    elseif ( $opr == '*' ) $now = $left * $right;
    elseif ( $opr == '/' ) $now = $left / $right;
    elseif ( $opr == '%' ) $now = (int) $left % (int) $right;

  }

?>
