<?php

  $pad_filter_start = $pad_prms_tag ['start'] ?? 1;
  $pad_filter_end   = $pad_prms_tag ['end'] ?? count($pad_data[$pad]);

  pad_set_arr_var ('done', 'start', TRUE);
  pad_set_arr_var ('done', 'end',   TRUE); 
  pad_data_filter_go ($pad_data[$pad], $pad_filter_start, $pad_filter_end);    

?>