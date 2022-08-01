<?php

  pad_timing_start ('build');

  pad_reset (2, $pad_lvl);

  include 'level.php';

  $page     = pad_get_page ($app, $page_next);
  $pad_next = '';

  $pad_base [1] = '';

  include "$pad_build_mode.php";

  if ( $pad_trace )
    pad_file_put_contents ("$pad_trace_dir_base/html-base.html", $pad_base [1] );
  
  include PAD . 'occurrence/start.php';

  pad_timing_end ('build');

?>