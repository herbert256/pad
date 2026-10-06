<?php

  // Unary ! on an array: an empty array negates to true ('1'), any populated one to false ('').
  //
  // A sign before an array reads it as the binary operators do (doubleArrVar.php): a list of
  // one is that value, a list of numbers their sum, and anything else has no one number - an
  // error under the strict check, empty in the lenient walk. 5 * array:one was 15 where
  // 5 * -array:one ended the request on "'array' is not a number".

  if ( $opr == 'NEG' or $opr == 'POS' ) {

    if ( count ( $right ) == 1 )
      $right = reset ( $right );
    elseif ( padEvalNumeric ( $right ) )
      $right = array_sum ( $right );
    else {

      global $padCheckSyntax;

      if ( $padCheckSyntax )
        padError ( ( $opr == 'NEG' ? '-' : '+' ) . " on an array of " . count ( $right ) . ": only an array of numbers sums to one value" );

      $right = '';

    }

    $now = ( $opr == 'NEG' ) ? - padEvalNumber ( $right, '-' ) : + padEvalNumber ( $right, '+' );

  } else

    $now = ( count ( $right ) ) ? '' : '1';

?>
