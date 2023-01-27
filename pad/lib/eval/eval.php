<?php
  

  function padEval ($eval, $myself='') {

 #   if ( strlen(trim($eval)) == 0 )
 #     return '';

    padTimingStart ('eval');

    $result = [];

    padEvalParse ( $result, $eval, $myself );
    padEvalAfter ( $result, $eval );  
    padEvalGo    ( $result, array_key_first($result), array_key_last($result), $myself) ;

    $key = array_key_first ($result);
      
    if     ( count($result) < 1        ) return padError("No result back");
    elseif ( count($result) > 1        ) return padError("More then one result back");
    elseif ( isset($result[$key][4])   ) return padError("Result is an array");
    elseif ( $result[$key][1] <> 'VAL' ) return padError("Result is not a value");

    padTimingEnd ('eval');
 
    return $result [$key] [0];

  }  


?>