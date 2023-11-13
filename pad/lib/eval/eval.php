<?php


  function padEval ( $eval, $value='' ) {

    if ( in_array ( $eval, $GLOBALS ['padEvalFast'] ) )
      return include pad . "_functions/$eval.php";

    return padEval2 ( $eval, $value );

    set_error_handler ( 'padErrorThrow' );

    try {

      $return = padEval2 ( $eval, $value );
    
    } catch (Throwable $e) {
    
      $return = padEvalCatch ( $e, $eval );
    
    }

    restore_error_handler ();

    return $return;

  }  


  function padEval2 ( $eval, $value ) {

    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'trace/items/eval/start.php';

    if ( strlen(trim($eval)) == 0 )
      return ''; 

    $result = [];

    padEvalParse ( $result, $eval, $value );    
 
    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'trace/items/eval/parse.php';

    padEvalAfter ( $result );  
 
    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'trace/items/eval/after.php';

    padEvalGo ( $result, array_key_first($result), array_key_last($result), $value) ;
 
    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'trace/items/eval/go.php';

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padThrow ("No result back: $eval");
    elseif ( count($result) > 1        ) padThrow ("More then one result back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padThrow ("Result is not a value: $eval");

    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'trace/items/eval/end.php';

    return $result [$key] [0];

  }  
  

  function padEvalCatch ( $e, $eval ) {

    include pad . 'trace/items/eval/error.php';

    return $eval;

  } 


?>