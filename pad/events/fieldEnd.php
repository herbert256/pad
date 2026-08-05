<?php

  // Fires from padField() (pad/lib/field/field.php) after the raw lookup result has been
  // coerced into the shape the caller's type expects (bool for the check types, '' or []
  // for the value types), immediately before it is returned.
  //
  // Closing half of events/fieldStart.php: traces the same field, type and level plus the
  // JSON-encoded return value when $padInfoTraceField is set.

  global $padInfoTrace, $padInfoTraceField;

  if ( $padInfoTrace and $padInfoTraceField )
    padInfoTrace ( 'field', 'end',
      ' field='  . $field .
      ' type='   . $type  .
      ' level='  . $lvl   .
      ' return=' . padJson ( $return )
    );

?>