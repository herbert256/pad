<?php


  function padEvalResult ( $result, $value, $eval ) {

    foreach ( $result as $key => $val ) 
      if ( $val [1] == '$$' ) {
        $result [$key] [0] = $value;
        $result [$key] [1] = 'VAL';
      }

    padEvalArray  ( $result, $value );
    padEvalOpnCls ( $result, $value );
    padEvalCheck  ( $result, $value );
    padEvalOpr    ( $result, $value );
    padEvalCheck  ( $result, $value );

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padError ("No value back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one value back: $eval");
    elseif ( $result[$key][1] <> 'VAL' ) padError ("Result is not a value: $eval");

    return $result [$key] [0];

  }


?>