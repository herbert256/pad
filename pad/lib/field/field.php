<?php


  function pField_check ($padarm) { return pField ($padarm, 1); } 
  function pField_value ($padarm) { return pField ($padarm, 2); } 
  function pArray_check ($padarm) { return pField ($padarm, 3); } 
  function pArray_value ($padarm) { return pField ($padarm, 4); } 
  function pField_null  ($padarm) { return pField ($padarm, 5); } 


  function pField ($padarm, $type) {

    $field = ( substr ( $padarm, 0, 1 ) == '$' ) ? substr ( $padarm, 1 ) : $padarm;

    if     ( strpos ( $field, '#' ) !== FALSE ) $value = pField_tag    ( $field        );
    elseif ( strpos ( $field, ':' ) !== FALSE ) $value = pField_prefix ( $field, $type );
    else                                        $value = pField_level  ( $field, $type );

    if     ($type == 1) return ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type == 2) return ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? ''    : $value;
    elseif ($type == 3) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type == 4) return ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? []    : $value;
    elseif ($type == 5) return ( $value === NULL                                               ) ? TRUE  : FALSE;

  }


?>