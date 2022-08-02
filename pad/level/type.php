<?php

  $pad_tag_cnt [$pad_lvl]++;

  ob_start();
  $pad_tag_content  = '';

  if ( $GLOBALS['pad_trace_tag'] )
    include 'trace/tag/before.php';

  pad_timing_start ('tag');
  $pad_tag_result = include PAD . "types/$pad_tag_type.php";
  pad_timing_end ('tag');

  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = pad_xxx_to_array ( $pad_tag_result );

  if ( $GLOBALS['pad_trace_tag'] )
    include 'trace/tag/after.php';

  $pad_tag_content .= ob_get_clean();
  
  $pad_walks [$pad_lvl] = $pad_walk; 
  
  return $pad_tag_result;

?>