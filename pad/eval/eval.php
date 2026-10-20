<?php

  // Entry point of the expression evaluator: turns the expression string $eval into a value.
  //
  // Reached from padEval() / padEvalBool() via try/try.php; $eval is the expression, $value
  // the incoming (piped) value. An expression that is nothing but the name of a built-in pipe
  // function short-circuits to eval/fast.php; otherwise it is split into tokens, parsed into a
  // tree and the tree evaluated, segment by segment, each segment taking the previous one's
  // result as its input. Returns the final value.

  // The trace keeps the stages of the evaluation, in globals padEvalTrace fills - begun
  // here with the expression, so one the validation refuses is no trace of the one before
  // it. The fast paths are left aside while it records.

  global $padEvalTrace;

  if ( $padEvalTrace ) {
    $GLOBALS ['_eval']      = [];
    $GLOBALS ['_eval_last'] = [];
    padEvalTrace ( 'expression', $eval );
  }

  // Only a plain name takes the fast path: the expression text went into the include path
  // as it was, so ../ reached any .php file under the engine.

  if ( ! $padEvalTrace and preg_match ( '/^[A-Za-z][A-Za-z0-9_]*$/', $eval ) and padFileExists ( PAD . "functions/$eval.php" ) )
    return include PAD . 'eval/fast.php';

  // The commonest expressions - a number, a string with no quote or backslash in it, a plain
  // $field - come once per row and per parameter, and answer here what the pipeline answers
  // for them: the number and the string as written, the field after the strict check
  // padEvalLeaves gives it. Not in a pipe body, where a signed number alone is named as an
  // operator missing its space, nor while the trace records the stages.

  if ( ! $pipe and is_string ( $eval ) and ! $padEvalTrace ) {

    $padEvalFirst = $eval [0] ?? '';

    if ( ( ctype_digit ( $padEvalFirst ) or $padEvalFirst == '-' ) and preg_match ( '/^-?\d+(\.\d+)?$/D', $eval ) )
      return $eval;

    if ( ( $padEvalFirst == "'" or $padEvalFirst == '"' ) and preg_match ( '/^(\'[^\'\\\\]*\'|"[^"\\\\]*")$/D', $eval ) )
      return padUnescape ( substr ( $eval, 1, -1 ) );

    if ( $padEvalFirst == '$' and preg_match ( '/^\$([a-zA-Z_][a-zA-Z0-9_]*)$/D', $eval, $padEvalField ) ) {
      $padEvalValue = padFieldValue ( $padEvalField [1] );
      if ( $GLOBALS ['padCheckSyntax'] and $padEvalValue === '' )
        padEvalFieldReport ( $padEvalField [1], $eval );
      return $padEvalValue;
    }

  }

  // A malformed expression is reported here, in the source's own terms, before the
  // scanner can turn it into something obscure downstream. A reported fault ends the
  // evaluation with an empty value. padEvalTokens (lib/eval/scan.php) validates and
  // scans, and keeps the tokens of a clean expression for the rest of the request.

  $padEvalTokens = padEvalTokens ( $eval );

  if ( $padEvalTokens === FALSE )
    return '';

  padEvalTrace ( 'tokens', $padEvalTokens );

  // A word standing alone after a | must be a pipe function, not a silently-swallowed
  // constant. In a pipe body - the expression a tag or variable pipe applies - the head
  // word is a function too, so $pipe extends the check to it; in a general expression the
  // head is a value and is left alone.

  if ( ! padEvalCheckPipes ( $padEvalTokens, $eval, $pipe ) )
    return '';

  // The tree of the expression (lib/eval/tree.php), kept for the request, is evaluated with
  // the value piped in (lib/eval/run.php).

  $padEvalTree = padEvalTreeGet ( $padEvalTokens, $eval, $pipe );

  padEvalTrace ( 'tree', $padEvalTree );

  return padEvalSegments ( $padEvalTree, $value );

?>
