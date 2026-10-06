<?php

  // Unary ! on a scalar: inverts truthiness into PAD's string booleans, '1' for a falsy
  // operand and '' for a truthy one. The signs NEG and POS - a - or + the tokeniser found
  // before a value that is no digit - read the operand as a number and negate it or keep
  // it (padEvalNumber).

  if     ( $opr == 'NEG' ) $now = - padEvalNumber ( $right, '-' );
  elseif ( $opr == 'POS' ) $now = + padEvalNumber ( $right, '+' );
  else                     $now = ( $right ) ? '' : '1';

?>
