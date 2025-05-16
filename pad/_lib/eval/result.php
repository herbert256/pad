<?php


  function padEvalResult ( $result, $value, $eval ) {
    
    padEvalValue  ( $result, $value );
    padEvalArray  ( $result, $value );
    padEvalOpnCls ( $result, $value );
    padEvalOpr    ( $result, $value );
    padEvalMulti  ( $result );

    $key = array_key_first ($result);

    $GLOBALS ['eval_result'] = $result;

    if     ( count($result) < 1        ) padError ("No result back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one reault back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    return $result [$key] [0];

  }


?>