<?php

  function padEvalResult ( $result, $value, $eval ) {

    padEvalValue  ( $result, $value );  padEvalTrace ( 'value1', $result );
    padEvalArray  ( $result, $value );  padEvalTrace ( 'array1', $result );
    padEvalOpnCls ( $result, $value );  padEvalTrace ( 'opncls1', $result );
    padEvalOpr    ( $result, $value );  padEvalTrace ( 'opr3', $result );
    padEvalMulti  ( $result );          padEvalTrace ( 'multi1', $result );

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padError ("No result back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one reault back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    return $result [$key] [0];

  }

?>
