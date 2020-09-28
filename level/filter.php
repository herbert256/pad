<?php

  $pad_filter_start = 1;
  $pad_filter_end   = count($pad_data[$pad_lvl]);

  if ( isset($pad_parms_tag['page']) and ! isset($pad_filter['page']) ) {
    $pad_filter_page  = (int) $pad_parms_tag ['page'];
    $pad_filter_rows  = (int) ($pad_parms_tag ['rows'] ?? 10);
    $pad_filter_start = ( ($pad_filter_page-1) * $pad_filter_rows ) + 1;
    $pad_filter_end   = ($pad_filter_start + $pad_filter_rows) - 1;
    pad_set_arr_var ('filter', 'page', TRUE);
    pad_set_arr_var ('filter', 'rows', TRUE); 
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_tag['rows']) and ! isset($pad_filter['rows']) ) {
    $pad_filter_end = ($pad_filter_start + $pad_parms_tag ['rows']) - 1;
    pad_set_arr_var ('filter', 'rows', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_tag['top']) and ! isset($pad_filter['top']) ) {
    $pad_filter_end = ($pad_filter_start + $pad_parms_tag ['top']) - 1;
    pad_set_arr_var ('filter', 'top', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_tag['bottom']) and ! isset($pad_filter['bottom']) ) {
    $pad_filter_start = ($pad_filter_end - $pad_parms_tag ['bottom']) + 1;
    pad_set_arr_var ('filter', 'bottom', TRUE);      
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

  if ( isset($pad_parms_tag['row']) and ! isset($pad_filter['row']) ) {
    $pad_filter_start = $pad_filter_end = $pad_parms_tag ['row'];
    pad_set_arr_var ('filter', 'row', TRUE);  
    pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    
  }

?>