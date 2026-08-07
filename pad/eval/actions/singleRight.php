<?php

  // Unary operator (! / not) whose right-hand neighbour is another operator, so it has no
  // operand of its own: the value piped into the segment ($myself) is negated instead.
  //
  // Nothing is consumed from $result; eval/go/go.php leaves the result in $b and padEvalOpr()
  // then folds the operator that followed.
  //
  // Nothing reaches this file. padEvalOpr() includes it from the one branch in operations.php
  // that wants a unary operator followed by an operator, but padEvalDouble() runs first on every
  // call and collapses every adjacent operator pair before the precedence walk begins, so that
  // shape never survives to be seen - {echo 0 | ! + 1}, {echo 2 | ! *}, {echo '' | 1 . ! + 2}
  // and the rest all go through alone.php instead, and answer sensibly. It is left here because
  // the branch that names it is still there; the two would have to go together.

  $right = $myself;

  include PAD . 'eval/go/go.php';

?>