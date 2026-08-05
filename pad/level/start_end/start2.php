<?php

  // Second half of the @start@ construct: the prelude has rendered, so the body put aside
  // by start_end/start1.php becomes the level's text again, the real data is restored and
  // rewound, and the normal occurrence loop starts.

  $padBase [$pad] = $padStartBase [$pad];
  $padData [$pad] = $padStartData [$pad];

  $padStartBase [$pad] = '';
  $padStartData [$pad] = [];

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>