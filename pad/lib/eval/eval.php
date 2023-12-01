<?php


  function padEvalFast ( $eval, $value ) {

    if ( padXref ) 
      include pad . 'tail/types/xref/items/fast.php';

    if ( $GLOBALS ['padTraceActive'] )
      return include pad . 'tail/events/eval/fast.php';
    else
      return include pad . "_functions/$eval.php";

  }  


  function padEval ( $eval, $value='' ) {

    if ( file_exists( "_functions/$eval.php" ) )
      return padEvalFast ( $eval, $value );

    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/events/eval/start.php';

    if ( strlen(trim($eval)) == 0 )
      return ''; 

    $result = [];

    padEvalParse ( $result, $eval, $value );    
 
    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/events/eval/parse.php';

    padEvalAfter ( $result );  
 
    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/events/eval/after.php';

    padEvalGo ( $result, array_key_first($result), array_key_last($result), $value) ;
 
    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/events/eval/go.php';

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padThrow ("No result back: $eval");
    elseif ( count($result) > 1        ) padThrow ("More then one result back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padThrow ("Result is not a value: $eval");

    if ( $GLOBALS ['padTraceActive'] )
      include pad . 'tail/events/eval/end.php';

    return $result [$key] [0];

  }  
  

  function padEvalCatch ( $e, $eval ) {

    include pad . 'tail/events/eval/error.php';

    return $eval;

  } 


?>