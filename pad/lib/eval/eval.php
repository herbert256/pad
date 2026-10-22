<?php

  // Public face of the expression evaluator; loaded once by inits/lib.php like every file
  // under lib/.
  //
  //   padEval      evaluates the expression text $eval against the incoming pipe value
  //                $value and returns the result - $pipe when the text is a pipe body,
  //                $fallback when the caller hands the answer to a fallback, so a field
  //                that is the whole answer may be missing. The pipeline itself lives in
  //                eval/eval.php, entered through try/try.php so that $padErrorTry can
  //                turn a thrown error into a PAD error rather than a fatal
  //   padEvalBool  the same, reduced to TRUE/FALSE - used by {if}, {while} and friends.
  //                An array counts as TRUE when it is non-empty
  //   padEvalTrace records the tokens and the tree of an evaluation in $_eval, for
  //                debugging the evaluator
  //
  // The stages are the siblings of this file: validate.php, scan.php (the tokens),
  // tree.php (the parser) and run.php (the evaluator).

  function padEvalTrace ( $type, $result ) {

    global $padEvalTrace;

    if ( ! $padEvalTrace )
      return;

    global $_eval, $_eval_last;

    if ( $result != $_eval_last )
      $_eval [] [$type] = $result;

    $_eval_last = $result;

  }

  // An arithmetic operand as a number: an int or float as it is, a boolean as 0 or 1, empty
  // or NULL as 0, a numeric string by what it spells (exponents included). Anything else
  // is no number: a strict-mode error naming it, and 0 in the lenient walk.

  function padEvalNumber ( $value, $opr ) {

    if ( is_int ( $value ) or is_float ( $value ) )
      return $value;

    if ( is_bool ( $value ) or $value === NULL or ( is_string ( $value ) and trim ( $value ) === '' ) )
      return (int) $value;

    if ( is_scalar ( $value ) and is_numeric ( trim ( (string) $value ) ) )
      return trim ( (string) $value ) + 0;

    global $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "'" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : gettype ( $value ), 40 ) . "' is not a number for $opr" );

    return 0;

  }

  function padEvalBool ( $eval, $value='' ) {

    $eval = padEval ( $eval, $value );

    if ( is_array ( $eval ) )
      if ( count ( $eval ) ) return TRUE;
      else                   return FALSE;
    else
      if ( $eval  ) return TRUE;
      else          return FALSE;

  }

  // $padEvalBusy counts the evaluations in flight. padAtSetTag() reads it: an @ reference
  // resolving inside an expression must not mark the level the expression belongs to, and
  // this is what tells that situation apart from a reference standing as a tag. A throw that
  // goes out of the evaluation - the try guard off, as it is while an error hook runs -
  // closes it as well: the count stayed one up for the rest of the request.
  //
  // A number and a string with no quote or backslash in it answer here, as written - what the
  // pipeline answers for them - before the try guard: its two includes cost several times what
  // the answer does, and a run of eval='4' asks for ten thousand of them, each term of the
  // reach build/check.php looks a value up in. Neither can throw, raise an error or resolve an
  // @ reference. Not in a pipe body, where a signed number alone is named as an operator
  // missing its space, nor while the trace records the stages.

  function padEval ( $eval, $value='', $pipe=FALSE, $fallback=FALSE ) {

    global $padEvalBusy, $padEvalTrace;

    if ( ! $pipe and is_string ( $eval ) and ! $padEvalTrace ) {

      $padEvalFirst = $eval [0] ?? '';

      if ( ( ctype_digit ( $padEvalFirst ) or $padEvalFirst == '-' ) and preg_match ( '/^-?\d+(\.\d+)?$/D', $eval ) )
        return $eval;

      if ( ( $padEvalFirst == "'" or $padEvalFirst == '"' ) and preg_match ( '/^(\'[^\'\\\\]*\'|"[^"\\\\]*")$/D', $eval ) )
        return padUnescape ( substr ( $eval, 1, -1 ) );

    }

    $padEvalBusy = ( $padEvalBusy ?? 0 ) + 1;

    try {
      return include PAD . 'try/eval/eval.php';
    } finally {
      $padEvalBusy--;
    }

  }

?>
