<?php

  // Binary operator with a value on either side - the ordinary a + b case.
  //
  // Included by padEvalOpr() with $f, $b and $k the keys of the left operand, the operator and
  // the right operand. Both operand tokens are consumed and eval/go/go.php leaves the result
  // in the operator's slot $b.

  $left = $result [$f] [0];
  $right = $result [$k] [0];

  unset ( $result [$f] );
  unset ( $result [$k] ); padEvalTrace ( 'action/double', $result );

  include PAD . 'eval/go/go.php';

?>