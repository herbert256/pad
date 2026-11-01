<?php

  // The rich form fields: the files of subs/inputs/types/ (padKinds, lib/subsystem.php) - a kind is
  // added by adding its PHP file there, and its script and style of the same name in
  // subs/inputs/assets/. The rest of the inputs is the subsystem in subs/inputs/, its library
  // subs/inputs/_lib/ loaded when a page writes one.

  function padInputTypes () {

    return padKinds ( 'inputs' );

  }

  function padInputType ( $name ) {

    return padKind ( 'inputs', $name );

  }

?>
