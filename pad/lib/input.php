<?php

  // The rich form fields: the files of inputs/types/ (padKinds, lib/subsystem.php) - a kind is
  // added by adding its PHP file there, and its script and style of the same name in
  // inputs/assets/. The rest of the inputs is the subsystem in inputs/, its library
  // inputs/_lib/ loaded when a page writes one.

  function padInputTypes () {

    return padKinds ( 'inputs' );

  }

  function padInputType ( $name ) {

    return padKind ( 'inputs', $name );

  }

?>
