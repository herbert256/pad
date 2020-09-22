<?php

  $pad_filter_start = 1;
  $pad_filter_end   = count($pad_data[$pad_lvl]);

  if ( isset($pad_parms_pad['page']) and ! isset($pad_filter['page']) ) {
    $pad_filter_page  = (int) $pad_parms_pad ['page'];
    $pad_filter_rows  = (int) ($pad_parms_pad ['rows'] ?? 10);
    $pad_filter_start = ( ($pad_filter_page-1) * $pad_filter_rows ) + 1;
    $pad_filter_end   = ($pad_filter_start + $pad_filter_rows) - 1;
    pad_set_arr_var ('filter', 'page', TRUE);
    pad_set_arr_var ('filter', 'rows', TRUE); 
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_pad['rows']) and ! isset($pad_filter['rows']) ) {
    $pad_filter_end = ($pad_filter_start + $pad_parms_pad ['rows']) - 1;
    pad_set_arr_var ('filter', 'rows', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_pad['top']) and ! isset($pad_filter['top']) ) {
    $pad_filter_end = ($pad_filter_start + $pad_parms_pad ['top']) - 1;
    pad_set_arr_var ('filter', 'top', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_pad['bottom']) and ! isset($pad_filter['bottom']) ) {
    $pad_filter_start = ($pad_filter_end - $pad_parms_pad ['bottom']) + 1;
    pad_set_arr_var ('filter', 'bottom', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_pad['row']) and ! isset($pad_filter['row']) ) {
    $pad_filter_start = $pad_filter_end = $pad_parms_pad ['row'];
    pad_set_arr_var ('filter', 'row', TRUE);  
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>