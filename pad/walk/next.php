<?php

  // One extra walk pass: re-runs the tag's handler to fetch the next block of data.
  //
  // Reached from level/end.php when the current data set is exhausted and the tag left
  // $padWalk [$pad] on 'next', which is how {while} and {until} keep looping. The handler
  // runs again through level/go, which recomputes the hit/else flags, and if it still asks
  // for more the fresh $padTagResult array becomes $padData [$pad] for another round of
  // occurrences. The flags are level/go's to set - included a second time here, every
  // option they fire fired twice.
  //
  // The fresh array is made data by padData(), as level/data.php makes the first pass's:
  // taken as it was, a list of values - ['c', 'd'] - reached occurrence/set.php as rows that
  // are no arrays, and the second pass ended the request on "foreach() argument must be of
  // type array".

  if ( $padInfo )
    include PAD . 'events/walk.php';

  $padWalk [$pad] = 'next';

  include PAD . 'try/level/go.php';

  if ( $padWalk [$pad] ) {

    if ( $padArray [$pad] )
      $padData [$pad] = padData ( $padTagResult );

    reset ( $padData [$pad] );

  }

?>
