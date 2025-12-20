<?php

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $error = $e->getFile() . ':' .  $e->getLine() . $e->getMessage();

  $padInfoTraceEvalData ['error'] = $error;

 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'error', $error );
 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'end',   $padInfoTraceEvalData );

?>