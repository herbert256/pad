<?php

  // A page's array - a db() result has this shape - searched by name from the template, and
  // the index kept for rows of one name made again when the rows change: the second search
  // of 'films' finds the film added, the third no longer the one removed.

  $films = [
    [ 'id' => 1, 'title' => 'The Running Man', 'plot' => 'A man runs for his life in a televised game.' ],
    [ 'id' => 2, 'title' => 'Chariots of Fire', 'plot' => 'Two athletes run in the 1924 Olympics.' ]
  ];

  $ids = function () use ( &$films ) {
    return implode ( ' ', array_column ( padSearch ( $films, 'run', [ 'name' => 'films', 'fields' => 'title^2, plot' ] ), 'id' ) );
  };

  $runners = [ $ids () ];

  $films [] = [ 'id' => 3, 'title' => 'Run Lola Run', 'plot' => 'Twenty minutes to find the money.' ];

  $runners [] = $ids ();

  array_shift ( $films );

  $runners [] = $ids ();

  $runners = implode ( ' / ', $runners );

  unset ( $ids );

?>
