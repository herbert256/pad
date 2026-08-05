<?php

  // scalar OP array, the mirror of doubleArrVar: a one-element array is unwrapped to its single
  // value and handed on to doubleVarVar.php. Longer arrays raise a 'ToDo' error.

  if ( count ( $right ) == 1 ) {

    $right = reset ( $right );

    include PAD . 'eval/go/doubleVarVar.php';

  } else

     padError ( 'ToDo' );

?>