<?php

  global $_eval, $_eval_after, $_eval_last, $_eval_parse;

  if ( file_exists ( PAD . "functions/$eval.php" ) )
    return include PAD . 'eval/fast.php';

  $_eval      = [];
  $_eval_last = [];

  padEvalParse ( $result, $eval );  padEvalTrace ( 'parse', $result ); $_eval_parse [] = $result;
  padEvalAfter ( $result );         padEvalTrace ( 'after', $result ); $_eval_after [] = $result;
  padEvalPipes ( $result, $pipes );

  foreach ( $pipes as $one )
    $value = padEvalResult ( $one, $value, $eval );

  return $value;

?>