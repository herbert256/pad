<?php

  // The toys: the files of toys/types/ (padKinds, lib/subsystem.php) - a toy is added by
  // adding its PHP file there, and its script and style of the same name in toys/assets/.
  // The rest of the toys is the subsystem in toys/, its library toys/_lib/ loaded when a page
  // shows one.

  function padToyTypes () {

    return padKinds ( 'toys' );

  }

  function padToyType ( $name ) {

    return padKind ( 'toys', $name );

  }

?>
