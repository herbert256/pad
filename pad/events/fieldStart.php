<?php

  global $padInfoTrace, $padInfoTraceField;

  if ( $padInfoTrace and $padInfoTraceField )
    padInfoTrace ( 'field', 'start',
      ' field='  . $field .
      ' type='   . $type .
      ' level='  . $lvl
    );

?>
