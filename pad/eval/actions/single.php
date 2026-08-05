<?php

  // Unary operator (! / not) with its operand to the right, as in {if ! $flag}.
  //
  // Included by padEvalOpr() with $b the operator and $k the value after it; that value token
  // is consumed and eval/go/go.php leaves the result in $b.

  $right = $result [$k] [0];

  unset ( $result [$k] ); padEvalTrace ( 'action/single', $result );

  include PAD . 'eval/go/go.php';

?>