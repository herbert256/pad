<?php

  $pad_build_now = $pad_build_base;

  $pad_exits     = [];
  $pad_build_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_now .= "/$pad_value";

    if ( is_dir ($pad_build_now) ) {

      $pad_call = "$pad_build_now/inits.php";
      include PAD_HOME . 'pad/level/call.php';

      $pad_exits [] = "$pad_build_now/exits.php";

    }

  }

  $pad_call = "$pad_build_now.php";
  $pad_base [1] .= include PAD_HOME . 'pad/level/call.php';

  foreach ( array_reverse ($pad_exits) as $pad_call )
    include PAD_HOME . 'pad/level/call.php';

  $pad_base [1] .= pad_get_html ( "$pad_build_base/pages/$page.html" );

?>