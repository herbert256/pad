<?php

  pad_reset (2, $pad_lvl);

  $page = $pad_next;
  $pad_next = '';

  pad_check_page ();

  $pad_lvl = 1;

  $pad_base [1] = '';

  include PAD . "build/$pad_build_mode.php";

  if ( $pad_trace )
    pad_file_put_contents ("$pad_trace_dir_base/base.html", $pad_base [1] );
  
  include PAD . 'occurrence/start.php';

?>