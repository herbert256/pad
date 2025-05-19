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

    if ( file_exists ( "functions/single/$eval.php" ) )
      return include 'eval/fast.php';

    $GLOBALS ['_eval'] = [];
    $GLOBALS ['_eval_last'] = [];
      
    padEvalParse ( $result, $eval );  padEvalTrace ( 'parse', $result );
    padEvalAfter ( $result );         padEvalTrace ( 'after', $result );
    padEvalPipes ( $result, $pipes ); 

    foreach ( $pipes as $one )
      $value = padEvalResult ( $one, $value, $eval );

    return $value;

  }  


?>