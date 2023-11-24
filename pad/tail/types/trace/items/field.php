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

?>