<?php
  

  function padEval ($eval, $myself='') {

    if ( strlen(trim($eval)) == 0 )
      return '';

    padTimingStart ('eval');

    $result = [];

    $GLOBALS ['eval_debug_1'] = $eval;
    padEvalParse ( $result, $eval, $myself );
    $GLOBALS ['eval_debug_2'] = $result;
    padEvalAfter ( $result, $eval );  
    $GLOBALS ['eval_debug_3'] = $result;
    padEvalGo    ( $result, array_key_first($result), array_key_last($result), $myself) ;
    $GLOBALS ['eval_debug_4'] = $result;

    $key = array_key_first ($result);
      
    if     ( count($result) < 1        ) return padError("No result back: $eval");
    elseif ( count($result) > 1        ) return padError("More then one result back: $eval");
    elseif ( isset($result[$key][4])   ) return padError("Result is an array: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) return padError("Result is not a value: $eval");

    padTimingEnd ('eval');
 
    return $result [$key] [0];

  }  


?>