<?php

  // Fires at the top of padField() (pad/lib/field/field.php), the single funnel every field
  // lookup passes through - padFieldValue, padFieldCheck, padArrayValue, padOptValue,
  // padTagValue and the rest are all thin wrappers over it - once the 'pad' shortcut has
  // been ruled out.
  //
  // Traces the field name, the numeric lookup type and the level when $padInfoTraceField
  // is set; the value it ends up with is logged by events/fieldEnd.php.

  global $padInfoTrace, $padInfoTraceField;

  if ( $padInfoTrace and $padInfoTraceField )
    padInfoTrace ( 'field', 'start',
      ' field='  . $field .
      ' type='   . $type .
      ' level='  . $lvl
    );

?>