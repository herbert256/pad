<?php

  pad_reset (2, $pad_lvl);

  $page = $pad_next;
  $pad_next = '';

  pad_trace ('build/start', "page=$page mode=$pad_mode");

  if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) )  
    pad_error ("Invalid page name '$page'");

  pad_check_page ();

  $pad_lvl = 0;

  if ( isset($_REQUEST['pad_include']) )
    $pad_mode = 'include';

  $pad_build_base = PAD_APPS . $app;

  $pad_html [0]  = '{true}';  
  $pad_html [0] .= '{content}';

  $pad_exits     = [];
  $pad_build_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_base .= "/$pad_value";

    if ( is_dir ($pad_build_base) ) {
      $pad_exits [] = "$pad_build_base/exits";
      $pad_one = "$pad_build_base/inits";
    }
    else
      $pad_one = $pad_build_base;

    $pad_build_now = include PAD_HOME . 'build/one.php';

    if ( strpos($pad_html [0], '{content}') !== FALSE)
      $pad_html [0] = str_replace('{content}', $pad_build_now, $pad_html [0]);
    else
      $pad_html [0] .= $pad_build_now;

  }

  foreach ( array_reverse ($pad_exits) as $pad_one )
    $pad_html [0] .= include PAD_HOME . 'build/one.php';

  $pad_html [0] .= '{/true}';

  pad_trace ('build/end', 'result=' . $pad_html [0] );

?>