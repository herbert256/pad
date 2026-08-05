<?php

  // Binary operator with no left operand, as in the pipe segment of {echo $x | + 1}: the value
  // piped into the segment ($myself) stands in as the left operand.
  //
  // Included by padEvalOpr() and padEvalCheck() with $b the operator and $k the right operand,
  // which is consumed; eval/go/go.php leaves the result in $b.

  $left  = $myself;
  $right = $result [$k] [0];

  unset ( $result [$k] ); padEvalTrace ( 'action/doubleleft', $result );

  include PAD . 'eval/go/go.php';

?>