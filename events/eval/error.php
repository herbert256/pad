<?php

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceEval'] )
    return;
  
  global $padInfoTraceEvalData;

  $error = $e->getFile() . ':' .  $e->getLine() . $e->getMessage();

  $padInfoTraceEvalData ['error'] = $error;

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'eval', 'error', $error );
 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'eval', 'end',   $padInfoTraceEvalData );

?>