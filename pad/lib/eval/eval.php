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

  function padEval ( $eval, $value='' ) {

    $padTry = 'eval/eval';
    return  include PAD . 'try/try.php';

  }

?>