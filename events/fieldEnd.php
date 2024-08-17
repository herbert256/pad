<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceField'] )
    padInfoTrace ( 'field', 'info', 
      ' field='  . $field . 
      ' type='   . $type  . 
      ' level='  . $lvl   .
      ' return=' . padJson ( $return )  
    );

?>