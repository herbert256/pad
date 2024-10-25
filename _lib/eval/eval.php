<?php


  function padEval ( $eval, $value='' ) {
    
    if ( $value and file_exists ( "/pad/functions/fast/$eval.php" ) )
      return '/pad/eval/fast.php';
      
    $result = [];

    padEvalParse  ( $result, $eval, $value );    
    padEvalAfter  ( $result );  
    padEvalArray  ( $result, $value );
    padEvalOpnCls ( $result, $value );
    padEvalOpr    ( $result, $value );

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padError ("No result back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one result back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    return $result [$key] [0];

  }  
 

?>