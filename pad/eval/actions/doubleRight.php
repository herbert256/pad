<?php

  // Mirror of doubleLeft: a binary operator with nothing usable to its right, so the value
  // piped into the segment ($myself) stands in as the right operand.
  //
  // Included by padEvalOpr() and padEvalCheck() with $f the left operand and $b the operator;
  // the left token is consumed and eval/go/go.php leaves the result in $b.

  $left = $result [$f] [0];
  $right = $myself;

  unset ( $result [$f] ); padEvalTrace ( 'action/doubleright', $result );

  include PAD . 'eval/go/go.php';

?>