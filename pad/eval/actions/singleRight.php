<?php

  // Unary operator (! / not) whose right-hand neighbour is another operator, so it has no
  // operand of its own: the value piped into the segment ($myself) is negated instead.
  //
  // Nothing is consumed from $result; eval/go/go.php leaves the result in $b and padEvalOpr()
  // then folds the operator that followed.

  $right = $myself;

  include PAD . 'eval/go/go.php';

?>