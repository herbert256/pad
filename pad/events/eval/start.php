<?php

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData           = [];
  $padInfoTraceEvalData ['eval']  = $eval;
  $padInfoTraceEvalData ['value'] = $value;

 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'start', "eval=$eval" );

?>