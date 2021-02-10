<?php

	$pad_filter_start = 1;
	$pad_filter_end   = ($pad_filter_start + $pad_parms_tag ['top']) - 1;

	pad_set_arr_var ('options_done', 'top', TRUE);      
	pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    

?>