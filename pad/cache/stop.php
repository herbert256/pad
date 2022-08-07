<?php

  $pStop = floor($pCache_stop);
  $pTime = $pCache_age;
  $pEtag = $pCache_etag;

  if ( $pTrace_cache )
    include 'trace.php';

  pTiming_end ('cache');

  pStop ($pStop);

?>