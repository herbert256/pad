<?php

  $pad_build_now = $pad_build_base;

  $pad_exits     = [];
  $pad_build_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_now .= "/$pad_value";

    if ( is_dir ($pad_build_now) ) {

      $pad_call = "$pad_build_now/inits.php";
      include PAD_HOME . 'level/call.php';

      $pad_exits [] = "$pad_build_now/exits.php";

    }

  }

  $pad_call = "$pad_build_now.php";
  $pad_html [0] .= include PAD_HOME . 'level/call.php';

  foreach ( array_reverse ($pad_exits) as $pad_call )
    include PAD_HOME . 'level/call.php';

  $pad_html [0] .= pad_get_html ( "$pad_build_base/pages/$page.html" );

?>