<?php

  if ( ! isset($pad_filter['start']) ) {
    $pad_filter_start = $pad_parms_tag ['start'] ?? 1;
    $pad_filter_end   = $pad_parms_tag ['end'] ?? count($pad_data[$pad_lvl]);
    pad_set_arr_var ('filter', 'start', TRUE);
    pad_set_arr_var ('filter', 'end',   TRUE); 
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>