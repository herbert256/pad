<?php

  // The kinds of game: the names of the PHP files of games/types/, sorted - a game is added by
  // adding its file, and its script and style of the same name in games/assets/. Read from the listing
  // kept between requests (padDirList, lib/dirs.php), as written on disk, as the chart kinds
  // are (padChartTypes, lib/chart.php). The tag types ask padGameType of every name nothing
  // else claims (lib/type.php); the rest of the games is the subsystem in games/, its
  // library games/_lib/ loaded when a page plays one.

  function padGameTypes () {

    static $types = NULL;

    if ( $types === NULL ) {
      $types = [];
      foreach ( padDirList ( PAD . 'games/types' ) ?: [] as $file )
        if ( $file [0] != '.' and str_ends_with ( $file, '.php' ) )
          $types [] = substr ( $file, 0, -4 );
      sort ( $types );
    }

    return $types;

  }

  function padGameType ( $name ) {

    static $types = NULL;

    $types ??= array_flip ( padGameTypes () );

    return is_string ( $name ) and isset ( $types [$name] );

  }

?>
