<?php

  $pad_build = '{content/}';

  $pad_build_src = $pad_build_base;
  $pad_build_mrg = pad_split ($pad_build_page, '/');
  
  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_html = $pad_build_now = '';
    $pad_build_src .= "/$pad_value";

    $pad_is_dir = ( is_dir ($pad_build_src) );

    if ( $pad_is_dir ) {
      $pad_include_php  = "$pad_build_src/inits.php";
      $pad_include_html = "$pad_build_src/inits.html";
    }
    else {
      $pad_include_php  = "$pad_build_src.php";
      $pad_include_html = "$pad_build_src.html";
    }

    if ( ! $pad_is_dir or ! isset($_REQUEST['pad_include']) )
      $pad_build_html = pad_html_get ($pad_include_html);

    if ( $pad_is_dir and strpos ($pad_build_html, '{content/}') === FALSE )
      $pad_build_html .= '{content/}';

    $pad_build_now .= "{build 'call' | '$pad_include_php'}" . $pad_build_html . "{/build}";

    if ( $pad_is_dir )
      $pad_build_now .= "{build 'call' | '$pad_build_src/exits.php'}" . pad_html_get ("$pad_build_src/exits.html") . "{/build}";

    $pad_build = str_replace ( '{content/}', $pad_build_now, $pad_build );

  }

  return $pad_build;
  
?>