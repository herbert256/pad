<?php

  // Fires from padEvalLook (lib/eval/run.php) when a name in an expression is resolved by a
  // pad/eval/single/<kind>.php handler - the value-like prefixes such as field:, data:,
  // local:, constant:, property:, parm:, level: or pull:.
  //
  // Xref only: files the name under functions/single, grouped by kind.

  global $padInfoXref;

  if ( $padInfoXref  )
    padInfoXref ( 'functions/single', $kind, $name );

?>
