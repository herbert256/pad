<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;
  
  global $padTraceEvalData;

  $padTraceEvalData ['result'] = $result [$key] [0];

  padTrace ( 'eval', 'result', $result [$key] [0] );
  padTrace ( 'eval', 'end',    $padTraceEvalData );

?>