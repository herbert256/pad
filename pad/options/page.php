<?php

  $pad_filter_page  = (int) ($pad_prms_tag ['page']  ?? 1);
  $pad_filter_rows  = (int) ($pad_prms_tag ['rows'] ?? 10);
  $pad_filter_start = ( ($pad_filter_page-1) * $pad_filter_rows ) + 1;
  $pad_filter_end   = ($pad_filter_start + $pad_filter_rows) - 1;

  pad_set_arr_var ('done', 'page', TRUE);
  pad_set_arr_var ('done', 'rows', TRUE); 
  pad_data_filter_go ($pad_data[$pad], $pad_filter_start, $pad_filter_end);    

?>