<?php

  // Fires from padAtType() (pad/at/_lib/at.php) just before an at/types/<go>.php handler
  // resolves the right-hand side of an @ reference - all, data, globals, sequences, tags.
  //
  // Xref only: records which of those type handlers a page actually reaches.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref  ( 'at', 'types', $go );

?>