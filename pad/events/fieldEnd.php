<?php

  global $padInfoTrace, $padInfoTraceField;

  if ( $padInfoTrace and $padInfoTraceField )
    padInfoTrace ( 'field', 'end',
      ' field='  . $field .
      ' type='   . $type  .
      ' level='  . $lvl   .
      ' return=' . padJson ( $return )
    );

?>
