<?php


  function padEval ( $eval, $myself='' ) {
    
    $result = [];

    padEvalParse  ( $result, $eval, $myself );    
    padEvalAfter  ( $result );  
    padEvalArray  ( $result, $myself );
    padEvalOpnCls ( $result, $myself );
    padEvalOpr    ( $result, $myself );

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padError ("No result back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one result back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    return $result [$key] [0];

  }  
 

?>