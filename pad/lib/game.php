<?php

  // The games: the files of games/types/ (padKinds, lib/subsystem.php) - a game is added by
  // adding its PHP file there, and its script and style of the same name in games/assets/.
  // The rest of the games is the subsystem in games/, its library games/_lib/ loaded when a
  // page plays one.

  function padGameTypes () {

    return padKinds ( 'games' );

  }

  function padGameType ( $name ) {

    return padKind ( 'games', $name );

  }

?>
