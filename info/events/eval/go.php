<?php

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceEval'] )
    return;

  global $padInfoTraceEvalData;

  $padInfoTraceEvalData ['go'] [] = $result;

?>