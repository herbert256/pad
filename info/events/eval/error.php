<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;
  
  global $padInfTraceEvalData;

  $error = $e->getFile() . ':' .  $e->getLine() . $e->getMessage();

  $padInfTraceEvalData ['error'] = $error;

 padTrace ( 'eval', 'error', $error );
 padTrace ( 'eval', 'end',   $padInfTraceEvalData );

?>