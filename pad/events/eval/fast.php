<?php

  // Unlike its siblings this is not a pure observer: it runs the built-in pipe function
  // PAD . functions/<eval>.php itself and returns the result, logging only when the
  // function actually changed the value.
  //
  // Dormant - nothing includes events/eval/*, and pad/eval/fast.php does that include
  // directly. Wiring it in as it stands would break the fast path: with tracing off it
  // returns NULL instead of the function's result.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  $return = include PAD . "functions/$eval.php";

  if ( $return != $value )
   if ( $padInfoTrace ) padInfoTrace ( 'eval', 'fast', "$eval: $value --> $return");

  return $return;

?>