<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceField'] )
    padInfoTrace ( 'field', 'info', 
      ' field='  . $field . 
      ' type='   . $type . 
      ' level='  . $lvl  
    );

  if ( $GLOBALS ['padInfoXref'] )
    padInfoXref ( 'field-types', $type, $field );
 
?>