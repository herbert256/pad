<?php

  global $padInfoTrace, $padInfoTraceEval;

  if ( ! $padInfoTrace or ! $padInfoTraceEval )
    return;

  $return = include PAD . "functions/$eval.php";

  if ( $return <> $value )
   if ( $padInfoTrace ) padInfoTrace ( 'eval', 'fast', "$eval: $value --> $return");

  return $return;

?>
