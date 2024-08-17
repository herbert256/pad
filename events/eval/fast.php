<?php

  if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceEval'] )
    return;

  $return = include "/pad/functions/$eval.php";

  if ( $return <> $value )
   if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'eval', 'fast', "$eval: $value --> $return");

  return $return;
   
?>