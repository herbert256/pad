<?php


  function pField_check ($parm) { return pField ($parm, 1); } 
  function pField_value ($parm) { return pField ($parm, 2); } 
  function pArray_check ($parm) { return pField ($parm, 3); } 
  function pArray_value ($parm) { return pField ($parm, 4); } 
  function pField_null  ($parm) { return pField ($parm, 5); } 


  function pField ($parm, $type) {

    $field = ( substr ( $parm, 0, 1 ) == '$' ) ? substr ( $parm, 1 ) : $parm;

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