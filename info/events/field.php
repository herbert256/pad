<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $GLOBALS ['padInfoTraceField'] )
    return;

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'field', 'info', 
    ' field='  . $field . 
    ' type='   . $type . 
    ' prefix=' . $prefix . 
    ' idx='    . $idx . 
    ' parm='   . $parm . 
    ' return=' . padJson ( $return )  
  );

  if ( $type == 2 ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'field', 'var',    $field );
  if ( $type == 4 ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'field', 'array',  $field );
  if ( $type == 6 ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'field', 'option', $field );
  if ( $type == 8 ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'field', 'tag',    $field );

?>