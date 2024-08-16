<?php

  if ( ! $GLOBALS ['padTraceField'] )
    return;

 padTrace ( 'field', 'info', 
    ' field='  . $field . 
    ' type='   . $type . 
    ' prefix=' . $prefix . 
    ' idx='    . $idx . 
    ' parm='   . $parm . 
    ' return=' . padJson ( $return )  
  );

  if ( $type == 2 )padTrace ( 'field', 'var',    $field );
  if ( $type == 4 )padTrace ( 'field', 'array',  $field );
  if ( $type == 6 )padTrace ( 'field', 'option', $field );
  if ( $type == 8 )padTrace ( 'field', 'tag',    $field );

?>