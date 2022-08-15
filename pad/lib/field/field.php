<?php


  function padFieldCheck ($padarm) { return padField ($padarm, 1); } 
  function padFieldValue ($padarm) { return padField ($padarm, 2); } 
  function padArrayCheck ($padarm) { return padField ($padarm, 3); } 
  function padArrayValue ($padarm) { return padField ($padarm, 4); } 
  function padFieldNull  ($padarm) { return padField ($padarm, 5); } 


  function padField ($padarm, $type) {

    $field = ( substr ( $padarm, 0, 1 ) == '$' ) ? substr ( $padarm, 1 ) : $padarm;

    if     ( strpos ( $field, '#' ) !== FALSE ) $value = padFieldTag    ( $field        );
    elseif ( strpos ( $field, ':' ) !== FALSE ) $value = padFieldPrefix ( $field, $type );
    else                                        $value = padFieldLevel  ( $field, $type );

    if     ($type == 1) return ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type == 2) return ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type == 3) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type == 4) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type == 5) return ( $value === NULL                                               ) ? TRUE  : FALSE;

  }


?>