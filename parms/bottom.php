<?php

  if ( ! isset($pad_filter['bottom']) ) {
    $pad_filter_end   = count($pad_data[$pad_lvl]);
    $pad_filter_start = ($pad_filter_end - $pad_parms_tag ['bottom']) + 1;
    pad_set_arr_var ('filter', 'bottom', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>