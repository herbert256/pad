<?php

  pad_reset (2, $pad_lvl);

  $page = $pad_next;
  $pad_next = '';

  if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) )  
    pad_error ("Invalid page name '$page'");

  pad_trace ('build/start', "page=$page mode=$pad_build_mode");

  pad_check_page ();

  $pad_lvl = 0;

  $pad_build_base = PAD_APPS . $app;

  $pad_html [0] = '';

  include PAD_HOME . "build/$pad_build_mode.php";

  $pad_html [0] = '{true}' . $pad_html [0] . '{/true}';

  pad_trace ('build/end', 'result=' . $pad_html [0] );

?>