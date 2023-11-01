<?php

  if ( ! $GLOBALS ['padTraceItems'] ['eval'] )
    return;

  global $padTraceEvalShort, $padTraceEval;

  $padTraceEval ['eval'] = $eval;
  $padTraceEval ['value'] = $value;
  
  padTrace ( 'eval', 'start', "eval=$eval" );

?>