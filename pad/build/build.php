<?php

  pad_timing_start ('build');

  include 'level.php';

  $pad_base [1] = '';

  include "$pad_build_mode.php";

  if ( $pad_trace )
    pad_file_put_contents ("$pad_trace_dir_base/html-base.html", $pad_base [1] );
  
  include PAD . 'occurrence/start.php';

  pad_timing_end ('build');

?>