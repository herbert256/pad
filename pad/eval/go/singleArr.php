<?php

  // Unary ! on an array: an empty array negates to true ('1'), any populated one to false ('').
  // A sign before an array has no number to work on: padEvalNumber names it.

  if     ( $opr == 'NEG' ) $now = - padEvalNumber ( $right, '-' );
  elseif ( $opr == 'POS' ) $now = + padEvalNumber ( $right, '+' );
  else                     $now = ( count ( $right ) ) ? '' : '1';

?>
