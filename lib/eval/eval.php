<?php
  

  function padEval ($eval, $myself='') {

    if ( strlen(trim($eval)) == 0 )
      return '';

    padTimingStart ('eval');

    $result = [];

    $GLOBALS ['padEvalDebug1'] = $eval;

    padEvalParse ( $result, $eval, $myself );
    $GLOBALS ['padEvalDebug2'] = $result;
    
    padEvalAfter ( $result, $eval );  
    $GLOBALS ['padEvalDebug3'] = $result;
    
    padEvalGo    ( $result, array_key_first($result), array_key_last($result), $myself) ;
    $GLOBALS ['padEvalDebug4'] = $result;

    $key = array_key_first ($result);
      
    if     ( count($result) < 1        ) return padError("No result back: $eval");
    elseif ( count($result) > 1        ) return padError("More then one result back: $eval");
    elseif ( isset($result[$key][4])   ) return padError("Result is an array: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) return padError("Result is not a value: $eval");

    padTimingEnd ('eval');
 
    return $result [$key] [0];

  }  


?>