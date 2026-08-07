<?php

  // scalar OP array, the mirror of doubleArrVar: a one-element array is unwrapped to that
  // element, a longer one is replaced by the sum of its elements when every element and the
  // other operand are numbers, and anything else is an error. doubleVarVar.php does the work
  // once both sides are scalars.

  if ( count ( $right ) == 1 ) {

    $right = reset ( $right );

    include PAD . 'eval/go/doubleVarVar.php';

  } elseif ( is_numeric ( $left ) and padEvalNumeric ( $right ) ) {

    $right = array_sum ( $right );

    include PAD . 'eval/go/doubleVarVar.php';

  } else

    padError ( "$opr on a value and an array of " . count ( $right ) . ": only an array of numbers, against a number, sums to one value" );

?>
