<?php

  if ( file_exists ( "functions/single/$eval.php" ) )
    return include 'eval/fast.php';

  $GLOBALS ['_eval']      = [];
  $GLOBALS ['_eval_last'] = [];
    
  padEvalParse ( $result, $eval );  padEvalTrace ( 'parse', $result );
  padEvalAfter ( $result );         padEvalTrace ( 'after', $result );
  padEvalPipes ( $result, $pipes ); 

  foreach ( $pipes as $one )
    $value = padEvalResult ( $one, $value, $eval );

  return $value;

?>