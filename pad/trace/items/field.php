<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['field'] )
    return;

  padTrace ( 'field', 'info', 
    ' field='  . $field . 
    ' type='   . $type . 
    ' prefix=' . $prefix . 
    ' idx='    . $idx . 
    ' parm='   . $parm . 
    ' return=' . padJson ( $return )  
  );

?>