<?php

  // Records in $padInfoTraceEvalData['after'] the value left once an evaluation action has
  // been applied, i.e. the state between eval/go.php and eval/end.php.
  //
  // Dormant - nothing in the engine includes events/eval/*.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['after'] = $result;

?>