<?php

  if ( ! isset($pad_filter['rows']) ) {
    $pad_filter_start = 1;
    $pad_filter_end   = ($pad_filter_start + $pad_parms_tag ['rows']) - 1;
    pad_set_arr_var ('filter', 'rows', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>