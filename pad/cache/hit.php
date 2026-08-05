<?php

  // Cache hit: skips straight to the output stage instead of building the page.
  //
  // $padTime becomes the entry's age, so the max-age headers count from when the page was
  // cached rather than from now, and $padCacheStop records the status this hit produced,
  // which tells exits/output.php the body came from the cache and may still be gzipped.

  $padTime      = $padCacheAge;
  $padCacheStop = $padStop;

  include PAD . 'exits/output.php';

?>