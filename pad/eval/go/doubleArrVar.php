<?php

  // array OP scalar: the operator needs one value a side, so the array has to become one.
  //
  // A one-element array is unwrapped to that element, whatever it holds. A longer one is
  // replaced by the sum of its elements, which means something only when every element and the
  // other operand are numbers. Where they are not - a string among them, or the nested rows a
  // data name resolves to - there is no one value to hand on and the operator is an error.
  //
  // Either way doubleVarVar.php does the work once both sides are scalars.

  if ( count ( $left ) == 1 ) {

    $left = reset ( $left );

    include PAD . 'eval/go/doubleVarVar.php';

  } elseif ( padEvalNumeric ( $left ) and is_numeric ( $right ) ) {

    $left = array_sum ( $left );

    include PAD . 'eval/go/doubleVarVar.php';

  } else

    padError ( "$opr on an array of " . count ( $left ) . " and a value: only an array of numbers, against a number, sums to one value" );

?>
