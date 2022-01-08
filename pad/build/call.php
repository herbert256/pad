<?php

  $pad_build_now = substr(APP, 0, -1);

  $pad_exits     = [];
  $pad_build_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_build_mrg as $pad_build_key => $pad_build_value) {

    $pad_build_now .= "/$pad_build_value";

    if ( $pad_build_key == array_key_last($pad_build_mrg) 
       and (pad_file_exists("$pad_build_now.php") or pad_file_exists("$pad_build_now.html") ) ) {
 
      $pad_call = "$pad_build_now.php";
      $pad_base [1] .= include PAD . 'level/call.php';

    } elseif ( is_dir ($pad_build_now) ) {

      $pad_call = "$pad_build_now/inits.php";
      $pad_base [1] .= include PAD . 'level/call.php';

      $pad_exits [] = "$pad_build_now/exits.php";

    } else {

      $pad_call = "$pad_build_now.php";
      $pad_base [1] .= include PAD . 'level/call.php';

    }

  }

  foreach ( array_reverse ($pad_exits) as $pad_call )
    $pad_base [1] .= include PAD . 'level/call.php';

?>