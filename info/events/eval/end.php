<?php

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceEval'] )
    return;
  
  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['result'] = $result [$key] [0];

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'eval', 'result', $result [$key] [0] );
 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'eval', 'end',    $padInfoTraceEvalData );

?>