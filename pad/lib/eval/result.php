<?php

  // Stage four of the evaluator: reduces the tokens of one pipe segment to a single value
  // and returns it. $value is the segment's input (the previous pipe's result, or the
  // caller's starting value) and is threaded through as $myself so that @ and operators
  // with a missing operand can reach it; $eval is the original text, kept only for error
  // messages.
  //
  // Order matters: inject the input value, collapse [ ] arrays, collapse ( ) groups, apply
  // operators by precedence, then concatenate whatever adjacent literals are left. What
  // survives must be exactly one VAL token, otherwise the expression was malformed and
  // padError says so.

  function padEvalResult ( $result, $value, $eval ) {

    padEvalValue  ( $result, $value );  padEvalTrace ( 'value1', $result );
    padEvalArray  ( $result, $value );  padEvalTrace ( 'array1', $result );
    padEvalOpnCls ( $result, $value );  padEvalTrace ( 'opncls1', $result );
    padEvalOpr    ( $result, $value );  padEvalTrace ( 'opr3', $result );
    padEvalMulti  ( $result );          padEvalTrace ( 'multi1', $result );

    $key = array_key_first ($result);

    if     ( count($result) < 1        ) padError ("No result back: $eval");
    elseif ( count($result) > 1        ) padError ("More then one reault back: $eval");
    elseif ( $result[$key][1] != 'VAL' ) padError ("Result is not a value: $eval");

    return $result [$key] [0];

  }

?>