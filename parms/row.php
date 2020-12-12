<?php

  if ( ! isset($pad_filter['row']) ) {
    $pad_filter_start = $pad_filter_end = $pad_parms_tag ['row'];
    pad_set_arr_var ('filter', 'row', TRUE);  
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>