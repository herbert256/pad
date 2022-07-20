<?php

  $pad_tag_cnt [$pad_lvl]++;

  ob_start();
  $pad_tag_content  = '';

  if ( $GLOBALS['pad_trace_tag'] )
    include 'trace/tag/before.php';

  pad_timing_start ('tag');
  $pad_tag_result = include PAD . "types/$pad_tag_type.php";
  pad_timing_end ('tag');

  if ( $GLOBALS['pad_trace_tag'] )
    include 'trace/tag/after.php';

  $pad_tag_content .= ob_get_clean();

  if ( $pad_tag_result === NULL )
    return include PAD . "level/null.php"; 
  
  if ( $pad_walk == 'close' ) {
	  $pad_close_tags[$pad_tag] = $pad_tag;
  	$pad_walk == '';
  }

  $pad_walks [$pad_lvl]      = $pad_walk; 
  $pad_true_false [$pad_lvl] = pad_true_false ($pad_tag_result);
  
  return $pad_tag_result;

?>