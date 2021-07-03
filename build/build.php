<?php

  pad_reset (2, $pad_lvl);

  $page = $pad_next;
  $pad_next = '';

  if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) )  
    pad_error ("Invalid page name '$page'");

  if ( strpos($page, '__LIB') !== FALSE)
    pad_error ("Invalid page name '$page'");

  pad_trace ('build/start', "page=$page mode=$pad_build_mode");

  pad_check_page ();

  $pad_lvl = 0;

  $pad_build_base = PAD_APPS . $app;

  $pad_html [0] = '';

  if ( isset($_REQUEST['pad_build_reference']) )
    pad_build_reference ("build/$pad_build_mode/$pad_build_merge");

  include PAD_HOME . "build/$pad_build_mode.php";
  include PAD_HOME . "build/__LIB.php";

  $pad_html [0] = '{true}' . $pad_lib_result . $pad_html [0] . '{/true}';

  pad_trace ('build/end', 'result=' . $pad_html [0] );

?>