<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;

  global $padInfTraceEvalData;

  $padInfTraceEvalData           = [];
  $padInfTraceEvalData ['eval']  = $eval;
  $padInfTraceEvalData ['value'] = $value;
  
 padTrace ( 'eval', 'start', "eval=$eval" );

?>