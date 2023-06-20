<?php
  

  function padEval ($eval, $value='') {

    if ( in_array ( $eval, $GLOBALS ['padEvalFast'] ) )
      return include pad . "_functions/$eval.php";

    if ( strlen(trim($eval)) == 0 )
      return ''; 

    $result = [];

    padEvalParse ( $result, $eval, $value );    
    padEvalAfter ( $result, $eval );  
    padEvalGo    ( $result, array_key_first($result), array_key_last($result), $value) ;

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) return padError("No result back: $eval");
    elseif ( count($result) > 1        ) return padError("More then one result back: $eval");
    elseif ( isset($result[$key][4])   ) return padError("Result is an array: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) return padError("Result is not a value: $eval");

    $GLOBALS ['padHistory'] [] = "Eval: $eval - " . $result [$key] [0];

    return $result [$key] [0];

  }  


?>