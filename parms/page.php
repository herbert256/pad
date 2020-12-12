<?php

  if ( ! isset($pad_filter['page']) ) {
    $pad_filter_page  = (int) $pad_parms_tag ['page'];
    $pad_filter_rows  = (int) ($pad_parms_tag ['rows'] ?? 10);
    $pad_filter_start = ( ($pad_filter_page-1) * $pad_filter_rows ) + 1;
    $pad_filter_end   = ($pad_filter_start + $pad_filter_rows) - 1;
    pad_set_arr_var ('filter', 'page', TRUE);
    pad_set_arr_var ('filter', 'rows', TRUE); 
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>