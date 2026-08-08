<?php

  // Entry point of the expression evaluator: turns the expression string $eval into a value.
  //
  // Reached from padEval() / padEvalBool() via try/try.php; $eval is the expression, $value
  // the incoming (piped) value. An expression that is nothing but the name of a built-in pipe
  // function short-circuits to eval/fast.php; otherwise it is parsed into tokens, type- and
  // operator-resolved, split at the | pipes and evaluated segment by segment, each segment
  // taking the previous one's result as its input. The $_eval* globals collect snapshots for
  // the trace. Returns the final value.

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