<?php

  // Public face of the expression evaluator; loaded once by inits/lib.php like every file
  // under lib/.
  //
  //   padEval      evaluates the expression text $eval against the incoming pipe value
  //                $value and returns the result. The pipeline itself lives in
  //                eval/eval.php, entered through try/try.php so that $padErrorTry can
  //                turn a thrown error into a PAD error rather than a fatal
  //   padEvalBool  the same, reduced to TRUE/FALSE - used by {if}, {while} and friends.
  //                An array counts as TRUE when it is non-empty
  //   padEvalTrace records the token array after each stage of the pipeline in $_eval,
  //                skipping stages that changed nothing, for debugging the evaluator
  //
  // The helper functions that make up the pipeline are the siblings of this file.

  function padEvalTrace ( $type, $result ) {

    global $padEvalTrace;

    if ( ! $padEvalTrace )
      return;

    global $_eval, $_eval_last;

    if ( $result != $_eval_last )
      $_eval [] [$type] = $result;

    $_eval_last = $result;

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
  // this is what tells that situation apart from a reference standing as a tag.

  function padEval ( $eval, $value='' ) {

    global $padEvalBusy;

    $padEvalBusy = ( $padEvalBusy ?? 0 ) + 1;

    $padTry = 'eval/eval';
    $padEvalValue = include PAD . 'try/try.php';

    $padEvalBusy--;

    return $padEvalValue;

  }

?>