<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceField'] )
    padInfoTrace ( 'field', 'end',
      ' field='  . $field .
      ' type='   . $type  .
      ' level='  . $lvl   .
      ' return=' . padJson ( $return )
    );

?>
