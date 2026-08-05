<?php

  // array OP scalar: a one-element array is unwrapped to its single value and handed on to
  // doubleVarVar.php. Longer arrays are not implemented and raise a 'ToDo' error.

  if ( count ( $left ) == 1 ) {

    $left = reset ( $left );

    include PAD . 'eval/go/doubleVarVar.php';

  } else

     padError ( 'ToDo' );

?>