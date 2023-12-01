<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;

  global $padTraceEvalData;

  $padTraceEvalData           = [];
  $padTraceEvalData ['eval']  = $eval;
  $padTraceEvalData ['value'] = $value;
  
  padTrace ( 'eval', 'start', "eval=$eval" );

?>