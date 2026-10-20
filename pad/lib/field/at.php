<?php

  // Not a library of its own: this pulls the at/ subsystem's helpers into the same global
  // function space, so padFieldAt can call padAt for name@tag and dotted-path lookups.
  // inits/lib.php sweeps lib/ recursively at start-up, and that sweep reaches this file -
  // at/_lib/ sits outside lib/ and would otherwise never be loaded. Its listing is kept
  // between requests (lib/dirs.php), where a glob read the directory every time.

  foreach ( padDirList ( PAD . 'at/_lib' ) ?: [] as $padAt )
    if ( $padAt [0] != '.' and str_ends_with ( $padAt, '.php' ) )
      include_once PAD . "at/_lib/$padAt";

?>
