<?php

  $pad_filter_first = $pad_parms_tag ['first'];
  if ( ! $pad_filter_first )
    $pad_filter_first = 1;

	$pad_filter_start = 1;
	$pad_filter_end   = ($pad_filter_start + $pad_filter_first) - 1;
     
  pad_set_arr_var ('options_done', 'first', TRUE);      
	pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    

?>