<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;
  
  global $padTraceEvalData;

  $error = $e->getFile() . ':' .  $e->getLine() . $e->getMessage();

  $padTraceEvalData ['error'] = $error;

  padTrace ( 'eval', 'error', $error );
  padTrace ( 'eval', 'end',   $padTraceEvalData );

?>