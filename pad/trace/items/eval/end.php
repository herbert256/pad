<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;
  
  global $padTraceEvalData;

  padTrace ( 'eval', 'end', 'result=' . $result [$key] [0] );

  padTraceFile ( 'eval', 'pad', $padTraceEvalData );

?>