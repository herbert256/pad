<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;
  
  global $padInfTraceEvalData;

  $padInfTraceEvalData ['result'] = $result [$key] [0];

 padTrace ( 'eval', 'result', $result [$key] [0] );
 padTrace ( 'eval', 'end',    $padInfTraceEvalData );

?>