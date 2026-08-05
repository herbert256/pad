<?php

  // Closes an expression's trace record on the success path: stores the final value in
  // $padInfoTraceEvalData['result'] and logs both that value and the whole collected
  // record. events/eval/error.php does the same for the failure path.
  //
  // Dormant - nothing in the engine includes events/eval/*.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['result'] = $result [$key] [0];

 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'result', $result [$key] [0] );
 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'end',    $padInfoTraceEvalData );

?>