<?php
  

  function padEval ( $eval, $value='' ) {

    if ( $GLOBALS ['padTraceActive'] )
      return padEvalTrace ( $eval, $value );

    if ( in_array ( $eval, $GLOBALS ['padEvalFast'] ) )
      return include pad . "_functions/$eval.php";

    $result = [];

    padEvalParse ( $result, $eval, $value );    
    padEvalAfter ( $result );  
    padEvalGo    ( $result, array_key_first($result), array_key_last($result), $value ) ;

    return reset ( $result ) [0];

  }  


  function padEvalTrace ( $eval, $value ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      return padEvalTraceGo ( $eval, $value );
    
    } catch (Throwable $e) {
    
      padEvalTraceCatch ( $e );
    
    }

    restore_error_handler ();

  }  
  

  function padEvalTraceGo ( $eval, $value ) {

    include pad . 'trace/items/eval_start.php';

    if ( strlen(trim($eval)) == 0 )
      return ''; 

    $result = [];

    padEvalParse ( $result, $eval, $value );    
    padEvalAfter ( $result );  
    padEvalGo    ( $result, array_key_first($result), array_key_last($result), $value) ;

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) return padError("No result back: $eval");
    elseif ( count($result) > 1        ) return padError("More then one result back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) return padError("Result is not a value: $eval");

    include pad . 'trace/items/eval_end.php';

    return $result [$key] [0];

  }  
  

  function padEvalTraceCatch ( $e ) {


  } 


?>