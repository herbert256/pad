<?php

  $padStop = floor($padCacheStop);
  $padTime = $padCacheAge;
  $padEtag = $padCacheEtag;

  if ( $padTrace )
    include 'trace.php';

  padTimingEnd ('cache');

  padStop ($padStop);

?>