<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $GLOBALS ['padInfoTraceField'] )
    return;

  padInfoTrace ( 'field', 'info', 
    ' field='  . $field . 
    ' type='   . $type  . 
    ' level='  . $lvl   .
    ' return=' . padJson ( $return )  
  );

?>