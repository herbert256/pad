<?php


  function padFieldCheck ($parm) { return padField ($parm, 1); } 
  function padFieldValue ($parm) { return padField ($parm, 2); } 
  function padArrayCheck ($parm) { return padField ($parm, 3); } 
  function padArrayValue ($parm) { return padField ($parm, 4); } 
  function padFieldNull  ($parm) { return padField ($parm, 5); } 


  function padField ($parm, $type) {

    $field = ( substr ( $parm, 0, 1 ) == '$' ) ? substr ( $parm, 1 ) : $parm;

    if ( substr ( $field, 0, 1 ) == '-' and strpos ( $field, '#' ) === FALSE )
      $field .= '#';

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