<?php

  $pad_filter_start = $pad_filter_end = $pad_prms_tag ['row'];

  pad_set_arr_var ('done', 'row', TRUE);  
  pad_data_filter_go ($pad_data[$pad], $pad_filter_start, $pad_filter_end);    

?>