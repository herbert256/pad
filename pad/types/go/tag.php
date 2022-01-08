<?php

  $pad_tag_go = $pad_tag_base."tags/$pad_tag";

  $pad_tag_php = '';

  if ( pad_file_exists("$pad_tag_go.html") )
  	$pad_tag_content .= pad_get_html ("$pad_tag_go.html");

  if ( pad_file_exists("$pad_tag_go.php") ) {

    ob_start();

    if ( $pad_tag_base == APP )
      pad_timing_start ('app');

    $pad_tag_php = include "$pad_tag_go.php";

    if ( $pad_tag_base == APP )
      pad_timing_end ('app');

    if ( $pad_tag_php === 1 )
      $pad_tag_php = '' ;

    $pad_tag_content .= pad_build_location ( "$pad_tag_go.php", ob_get_clean() );

  }

  if ( is_array($pad_tag_php) or is_object($pad_tag_php) or is_resource($pad_tag_php) )
    return $pad_tag_php;

  if ( $pad_tag_php !== TRUE and $pad_tag_php !== FALSE and $pad_tag_php !== NULL ) {
    if ( $pad_tag_php or $pad_tag_content )
      $pad_tag_php = $pad_tag_content . $pad_tag_php;
    $pad_tag_content = '';
  }

  return $pad_tag_php;

?>