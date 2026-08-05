<?php

  // One extra walk pass: re-runs the tag's handler to fetch the next block of data.
  //
  // Reached from level/end.php when the current data set is exhausted and the tag left
  // $padWalk [$pad] on 'next', which is how {while} and {until} keep looping. The handler
  // runs again through level/go, the hit/else flags are recomputed, and if it still asks
  // for more the fresh $padTagResult array becomes $padData [$pad] for another round of
  // occurrences.

  if ( $padInfo )
    include PAD . 'events/walk.php';

  $padWalk [$pad] = 'next';

  $padTry = 'level/go';
  include PAD . 'try/try.php';

  include PAD . 'level/flags.php';

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] )
      $padData [$pad] = $padTagResult;

    reset ( $padData [$pad] );

  }

?>