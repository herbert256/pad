<?php

  // Type handler for a {macro} of the template: its body is the tag's template, rendered
  // once with the parameters of this use as the fields of its one row - lib/macro.php.

  $padTagContent = $padMacroStore [$padTag [$pad]] ['source'];

  $padMacroRow = padMacroRow ( $padTag [$pad] );

  return count ( $padMacroRow ) ? [ $padMacroRow ] : TRUE;

?>
