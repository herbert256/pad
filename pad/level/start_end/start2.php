<?php

  // Second half of the @start@ construct: the prelude has rendered, so the body put aside
  // by start_end/start1.php becomes the level's text again, the real data is restored and
  // rewound, and the normal occurrence loop starts.
  //
  // The prelude was occurrence 1, and the body went on counting from there: its first row
  // was occurrence 2, so first, even/odd and current all read one row off. The count starts
  // over for the body. A data set with no rows has no body to walk - opening one read a
  // row at a NULL key - so the level goes straight on to its @end@ coda, or closes; the
  // prelude, already banked, is not banked twice.

  $padBase [$pad] = $padStartBase [$pad];
  $padData [$pad] = $padStartData [$pad];

  $padStartBase [$pad] = '';
  $padStartData [$pad] = [];

  $padOccur [$pad] = 0;

  if ( ! count ( $padData [$pad] ) ) {
    $padOut [$pad] = '';
    return include PAD . 'level/end.php';
  }

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>