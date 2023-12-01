<?php

  if ( ! $GLOBALS ['padTraceEval'] )
    return;

  $return = include pad . "_functions/$eval.php";

  if ( $return <> $value )
    padTrace ( 'eval', 'fast', "$eval: $value --> $return");

  return $return;
   
?>