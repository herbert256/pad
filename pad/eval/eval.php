<?php

  if ( file_exists ( PAD . "functions/$eval.php" ) )
    return include PAD . 'eval/fast.php';

  $GLOBALS ['_eval']      = [];
  $GLOBALS ['_eval_last'] = [];

  padEvalParse ( $result, $eval );  padEvalTrace ( 'parse', $result ); $GLOBALS ['_eval_parse'] [] = $result;
  padEvalAfter ( $result );         padEvalTrace ( 'after', $result ); $GLOBALS ['_eval_after'] [] = $result;
  padEvalPipes ( $result, $pipes );

  foreach ( $pipes as $one )
    $value = padEvalResult ( $one, $value, $eval );

  return $value;

?>
