<?php

  if ( ! $GLOBALS ['padTraceItems'] ['eval'] )
    return;
  
  global $padTraceEvalShort, $padTraceEval;

  padTrace ( 'eval', 'end', 'result=' . $result [$key] [0] );

  if ( ! $padTraceEvalShort )
    padTraceFile ( 'eval', 'pad', $padTraceEval );

?>