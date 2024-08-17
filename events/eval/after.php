<?php

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceEval'] )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['after'] = $result;

?>