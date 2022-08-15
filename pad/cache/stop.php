<?php

  $padStop = floor($padCache_stop);
  $padTime = $padCache_age;
  $padEtag = $padCache_etag;

  if ( $padTrace )
    include 'trace.php';

  pTiming_end ('cache');

  pStop ($padStop);

?>