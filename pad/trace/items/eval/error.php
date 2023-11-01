<?php

  if ( ! $GLOBALS ['padTraceItems'] ['eval'] )
    return;
  
  global $padTraceEvalShort, $padTraceEval;

  $error = $e->getFile() . ':' .  $e->getLine() . ' LOG-ERROR: ' . $e->getMessage()

  padTrace ( 'eval', 'error', $error );

  padTraceFile ( 'eval-error', 'pad', $padTraceEval );

?>