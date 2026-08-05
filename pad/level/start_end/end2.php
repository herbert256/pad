<?php

  // Second half of the @end@ construct: the loop is exhausted, so the coda put aside by
  // start_end/end1.php becomes the level's text and is rendered against a single default
  // occurrence, after which level/end.php closes the level for real.

  $padBase [$pad] = $padEndBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padEndBase [$pad] = '';

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>