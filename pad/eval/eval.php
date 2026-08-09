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

  // A malformed expression is reported here, in the source's own terms, before the
  // tokeniser can turn it into something obscure downstream. A reported fault ends the
  // evaluation with an empty value.

  if ( ! padEvalValidate ( $eval ) )
    return '';

  $_eval      = [];
  $_eval_last = [];

  padEvalParse ( $result, $eval );  padEvalTrace ( 'parse', $result ); $_eval_parse [] = $result;

  // A word standing alone after a | must be a pipe function, not a silently-swallowed
  // constant. In a pipe body - the expression a tag or variable pipe applies - the head
  // word is a function too, so $pipe extends the check to it; in a general expression the
  // head is a value and is left alone.

  if ( ! padEvalCheckPipes ( $result, $eval, $pipe ) )
    return '';


  padEvalAfter ( $result );         padEvalTrace ( 'after', $result ); $_eval_after [] = $result;
  padEvalPipes ( $result, $pipes );

  foreach ( $pipes as $one )
    $value = padEvalResult ( $one, $value, $eval );

  return $value;

?>