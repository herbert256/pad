<?php

  // The catch-side counterpart of eval/end.php: flattens the caught exception $e to
  // file:line plus message, stores it in $padInfoTraceEvalData['error'] and closes the
  // record, so a failed evaluation still appears in the trace.
  //
  // Dormant - nothing in the engine includes events/eval/*.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $error = $e->getFile() . ':' .  $e->getLine() . $e->getMessage();

  $padInfoTraceEvalData ['error'] = $error;

 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'error', $error );
 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'end',   $padInfoTraceEvalData );

?>