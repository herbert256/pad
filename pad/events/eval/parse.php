<?php

  // Records the parsed form of an expression in $padInfoTraceEvalData['parse'], between
  // eval/start.php and the evaluation steps logged by eval/go.php.
  //
  // Dormant - nothing in the engine includes events/eval/*. Note it declares the global
  // $padInfoTraceData while writing to $padInfoTraceEvalData.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceData;

  $padInfoTraceEvalData ['parse'] = $result;

?>