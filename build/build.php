<?php

  pad_reset (2, $pad_lvl);

  $pad_build_store = [];

  $page = $pad_next;
  $pad_next = '';

  if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) )  
    pad_error ("Invalid page name '$page'");

  pad_check_page ();

  $pad_lvl   = 0;
  $pad_content_store = [];
  $pad_data_store  = [];
  
  $pad_build_base = PAD_APPS . $app;
  $pad_build_page = "pages/$page";

  $pad_html [0] = include PAD_HOME . "build/$pad_mode.php";
  $pad_html [0] = '{true}' . $pad_html [0] . '{/true}';

?>