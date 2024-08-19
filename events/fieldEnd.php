<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceField'] )
    padInfoTrace ( 'field', 'end', 
      ' field='  . $field . 
      ' type='   . $type  . 
      ' level='  . $lvl   .
      ' return=' . padJson ( $return )  
    );

    if ( ! $GLOBALS ['padInfoXref'] )
      return; 

    if     ($type ==  1) $ret = ( $value !== NULL and ( $value === INF or ! is_scalar($value) ) ) ? FALSE : TRUE;
    elseif ($type ==  2) $ret = ( $value === NULL or    $value === INF or ! is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type ==  3) $ret = ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type ==  4) $ret = ( $value === NULL or    $value === INF or   is_scalar($value)   ) ? FALSE : TRUE;
    elseif ($type ==  5) $ret = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  6) $ret = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  7) $ret = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  8) $ret = ( $value === INF                                                ) ? FALSE : TRUE;
    elseif ($type ==  9) $ret = ( $value === NULL                                               ) ? TRUE  : FALSE;

  if ( $ret ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padInfoXref ( 'fields', 'types', $type );
      padInfoXref ( 'fields', 'names', $field );
      padInfoXref ( 'fields/values', $field, $return );

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }


?>