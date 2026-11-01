<?php

  // The printables: the files of subs/printables/types/ (padKinds, lib/subsystem.php) - a
  // kind of page made for paper is added by adding its file there. The rest of the printables
  // is the subsystem in subs/printables/, its library subs/printables/_lib/ loaded when a page
  // shows one.

  function padPrintableKinds () {

    return padKinds ( 'printables' );

  }

  function padPrintableKind ( $name ) {

    return padKind ( 'printables', $name );

  }

?>
