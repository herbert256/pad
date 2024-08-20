<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceField'] )
    padInfoTrace ( 'field', 'start', 
      ' field='  . $field . 
      ' type='   . $type . 
      ' level='  . $lvl  
    );

?>