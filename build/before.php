<?php

  $pad_build = '';

  $pad_build_src = $pad_build_base;
  $pad_build_mrg = pad_split ($pad_build_page, '/');

  $pad_after = [];
 
  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_src .= "/$pad_value";

    if ( is_dir ($pad_build_src) ) {
      $pad_build .= pad_build_php ("$pad_build_src/inits");
      $pad_after []  = $pad_build_src;
    }
    else 
      $pad_build .= pad_build_php ($pad_build_src);
        
  }

  foreach ( array_reverse ($pad_after, TRUE) as $pad_build_src ) 
    $pad_build .= pad_build_php ("$pad_build_src/exits");

  $pad_build .= '{content}';

  $pad_build_src = $pad_build_base;
  
  foreach ($pad_build_mrg as $pad_value) {

    $pad_build_now = '';
    $pad_build_src .= "/$pad_value";

    $pad_is_dir = ( is_dir ($pad_build_src) );

    if ( ! $pad_is_dir or ! isset($_REQUEST['pad_include']) )
      if ( $pad_is_dir )
        $pad_build_now .= pad_build_html ("$pad_build_src/inits");
     else
        $pad_build_now .= pad_build_html ($pad_build_src);

    if ( $pad_is_dir and strpos ($pad_build_now, '{content}') === FALSE )
      $pad_build_now .= '{content}';

    if ( $pad_is_dir )
      $pad_build_now .= pad_build_html ("$pad_build_src/exits");

    $pad_build = str_replace ( '{content}', $pad_build_now, $pad_build );

  }

  return $pad_build;
  
?>