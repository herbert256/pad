<?php
  
  pad_timing_end ('cache');

  $pad_time = $pad_cache_age;
  $pad_etag = $pad_cache_etag;

  include PAD_HOME . 'exits/stop.php';

?>