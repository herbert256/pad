<?php


  function padEvalTrace ( $type, $result ) {

    global $_eval, $_eval_last;

    if ( $result <> $_eval_last )
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
    return  include 'try/try.php';

  }  


?>