<?php

  // Operator standing on its own, with no operand on either side: the value piped into the
  // segment ($myself) is used as both operands, so {echo 5 | *} squares it.
  //
  // Included from padEvalCheck() for a segment holding just an operator, and from
  // padEvalDouble() when two operators end up adjacent. $b is the operator's key; nothing is
  // consumed from $result and eval/go/go.php leaves the outcome there.

  $left  = $myself;
  $right = $myself;

  include PAD . 'eval/go/go.php';

?>