<?php

  // Fires from pad/eval/type/type.php for the other branch of the same choice: a name with
  // no pad/eval/single/ handler, evaluated with parameters instead through
  // pad/eval/parms/<kind>.php - kinds pad, app, php, tag and sequence.
  //
  // Xref only: files the name under functions/parms, grouped by kind.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'functions/parms', $kind, $name );

?>