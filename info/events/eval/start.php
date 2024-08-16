<?php

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceEval'] )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData           = [];
  $padInfoTraceEvalData ['eval']  = $eval;
  $padInfoTraceEvalData ['value'] = $value;
  
 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'eval', 'start', "eval=$eval" );

?>