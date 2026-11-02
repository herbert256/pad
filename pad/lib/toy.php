<?php

  // The toys: the files of subs/games/toys/types/ (padKinds, lib/subsystem.php) - a toy is added by
  // adding its PHP file there, and its script and style of the same name in www/assets/toys/.
  // The rest of the toys is the subsystem in subs/games/toys/, its library subs/games/toys/_lib/ loaded when a page
  // shows one.

  function padToyTypes () {

    return padKinds ( 'games/toys' );

  }

  function padToyType ( $name ) {

    return padKind ( 'games/toys', $name );

  }

?>
