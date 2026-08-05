<?php

  // Sub-event of the per-level trace dump (pad/info/types/trace/level/info.php), where it
  // would record use of the 'content' construct in the xref.
  //
  // Currently a no-op: the unconditional return below disables the reporting code.

  global $padInfoXref;

  return;

  if ( $padInfoXref  )
      padInfoXref ( 'constructs', 'content' );

?>