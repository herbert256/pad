<?php

  // Appends the outcome of one evaluation step to $padInfoTraceEvalData['go'], building the
  // step-by-step history of how an expression was reduced.
  //
  // Dormant - nothing in the engine includes events/eval/*.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['go'] [] = $result;

?>