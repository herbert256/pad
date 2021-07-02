<?php

  $pad_lib_result = '';
  $pad_lib_build  = PAD_APPS . $app;

  $pad_lib_mrg = pad_split ("pages/$page", '/');

  foreach ($pad_lib_mrg as $pad_lib_value) {

    $pad_lib_build .= "/$pad_lib_value";

    if ( is_dir ($pad_lib_build) ) {

      $pad_lib_php  = "$pad_lib_build/__LIB.php";
      $pad_lib_html = "$pad_lib_build/__LIB.html";

      if ( pad_file_exists($pad_lib_php) ) {
        $pad_call = $pad_lib_php;
        $pad_lib_result .= "{lib '$pad_lib_php'}";
        $pad_lib_result .= include PAD_HOME . 'level/call.php';
        $pad_lib_result .= "{/lib}";
      }

      if ( pad_file_exists($pad_lib_html) )
        $pad_lib_result .= "{lib '$pad_lib_html'} " . pad_get_html ( $pad_lib_html ) . " {/lib}";
      
    }

  }

?>