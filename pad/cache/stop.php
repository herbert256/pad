<?php

  $pad_stop = floor($pad_cache_stop);
  $pad_time = $pad_cache_age;
  $pad_etag = $pad_cache_etag;

  if ( $pad_trace )
    include 'trace.php';

  pad_stop ($pad_stop);

?>