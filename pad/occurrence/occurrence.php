<?php

  // Starts one occurrence - one pass of the current level over one row of its data.
  //
  // Opens the row (init), publishes its fields as variables (set) and runs the app's row
  // callback, unless a before= callback already walked the whole set up front. Reached
  // from level/end.php for every following row, and from build/build.php,
  // start/pad/code.php and level/start.php for the first one.

  include PAD . 'occurrence/init.php';

  if ( $padInfo )
    include PAD . 'events/occurStart.php';

  include PAD . 'occurrence/set.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
    include PAD . 'callback/row.php' ;

?>