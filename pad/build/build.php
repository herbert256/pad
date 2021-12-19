<?php

  pad_reset (2, $pad_lvl);

  $page = $pad_next;
  $pad_next = '';

  pad_check_page ();

  $pad_build_base = PAD_HOME . $app;
  $pad_lvl = 1;

  $pad_base [1] = '';

  include PAD_HOME . "pad/build/$pad_build_mode.php";
  include PAD_HOME . "pad/build/__LIB.php";

  $pad_base [1] = $pad_lib_result . $pad_base [1];

  if ( $pad_trace )
    pad_file_put_contents ("$pad_trace_dir_base/base.html", $pad_base [1] );
  
  include PAD_HOME . 'pad/occurrence/start.php';

?>