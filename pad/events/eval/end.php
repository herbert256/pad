<?php

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['result'] = $result [$key] [0];

 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'result', $result [$key] [0] );
 if ( $padInfoTrace ) padInfoTrace ( 'eval', 'end',    $padInfoTraceEvalData );

?>