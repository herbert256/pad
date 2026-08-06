<?php

  // Records the parsed form of an expression in $padInfoTraceEvalData['parse'], between
  // eval/start.php and the evaluation steps logged by eval/go.php.
  //
  // Dormant - nothing in the engine includes events/eval/*.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['parse'] = $result;

?>