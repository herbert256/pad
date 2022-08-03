<?php

  $pad_parameters [$pad_lvl] ['tag_cnt'];

  if ( $GLOBALS['pad_trace_tag'] )
    include 'trace/tag/before.php';

  $pad_tag_content  = '';
  ob_start();

  pad_timing_start ('tag');
  $pad_tag_result = include PAD . "types/$pad_tag_type.php";
  pad_timing_end ('tag');

  $pad_tag_content .= ob_get_clean();

  if ( $GLOBALS['pad_trace_tag'] )
    include 'trace/tag/after.php';

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );

  if ( $pad_tag_result === TRUE AND $pad_tag_content <> '' )
    $pad_tag_result = $pad_tag_content;

  if ( is_scalar($pad_tag_result) and strpos($pad_tag_result , '@content@') !== FALSE )
    $pad_tag_result = str_replace('@content@', $pad_content, $pad_tag_result);

  $pad_walks [$pad_lvl] = $pad_walk; 

?>