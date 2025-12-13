<?php


  /**
   * Records eval execution state for debugging.
   *
   * Stores result state changes in $_eval array for trace output.
   *
   * @param string $type   The trace point name.
   * @param array  $result The current result state.
   *
   * @return void
   *
   * @global array $_eval      Trace log array.
   * @global array $_eval_last Last recorded state.
   */
  function padEvalTrace ( $type, $result ) {

    global $_eval, $_eval_last;

    if ( $result <> $_eval_last )
      $_eval [] [$type] = $result;

    $_eval_last = $result;

  }


  /**
   * Evaluates expression and returns boolean result.
   *
   * Calls padEval and converts result to boolean. Arrays with
   * elements are TRUE, non-empty values are TRUE.
   *
   * @param string $eval  The expression to evaluate.
   * @param mixed  $value Optional value context.
   *
   * @return bool The boolean result.
   */
  function padEvalBool ( $eval, $value='' ) {

    $eval = padEval ( $eval, $value );

    if ( is_array ( $eval ) )
      if ( count ( $eval ) ) return TRUE;
      else                   return FALSE;
    else
      if ( $eval  ) return TRUE;
      else          return FALSE;

  }


  /**
   * Evaluates a PAD expression string.
   *
   * Main entry point for expression evaluation. Delegates to
   * eval/eval.php via try/try.php error handling wrapper.
   *
   * @param string $eval  The expression to evaluate.
   * @param mixed  $value Optional value context.
   *
   * @return mixed The evaluated result.
   */
  function padEval ( $eval, $value='' ) {

    $padTry = 'eval/eval';
    return  include PAD . 'try/try.php';

  }  


?>