<?php

  // Opens the trace record for one expression evaluation: resets $padInfoTraceEvalData and
  // seeds it with the expression and the value it is applied to, then logs 'eval start'.
  // The other events/eval/ files fill that record in and eval/end.php emits it.
  //
  // Dormant - nothing in the engine includes events/eval/*, so none of these seven fire,
  // even though $padInfoTraceEval is still defined in pad/config/info/.

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData           = [];
  $padInfoTraceEvalData ['eval']  = $eval;
  $padInfoTraceEvalData ['value'] = $value;

 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'start', "eval=$eval" );

?>