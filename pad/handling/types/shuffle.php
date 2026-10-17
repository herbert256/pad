<?php

  // Handles the shuffle option: puts the tag's data set in random order. The keys of keyed
  // rows go along - {&key} names its row, as after sort, reverse and random: PHP's shuffle()
  // renumbered them 0, 1, 2. A list stays a list.

  if ( array_is_list ( $padData [$pad] ) )
    shuffle ( $padData [$pad] );

  else {

    $padShuffleKeys = array_keys ( $padData [$pad] );
    shuffle ( $padShuffleKeys );

    $padShuffleRows = [];
    foreach ( $padShuffleKeys as $padShuffleKey )
      $padShuffleRows [$padShuffleKey] = $padData [$pad] [$padShuffleKey];

    $padData [$pad] = $padShuffleRows;

  }

?>
