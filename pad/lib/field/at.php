<?php

  // Not a library of its own: this pulls the at/ subsystem's helpers into the same global
  // function space, so padFieldAt can call padAt for name@tag and dotted-path lookups.
  // inits/lib.php sweeps lib/ recursively at start-up, and that sweep reaches this file -
  // at/_lib/ sits outside lib/ and would otherwise never be loaded.

  foreach  ( glob ( PAD . 'at/_lib/*.php' ) as $padAt )
    include_once "$padAt";

?>