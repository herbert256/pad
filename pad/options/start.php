<?php

  $pad_filter_start = $pad_parms_tag ['start'] ?? 1;
  $pad_filter_end   = $pad_parms_tag ['end'] ?? count($pad_data[$pad_lvl]);

  pad_set_arr_var ('options_done', 'start', TRUE);
  pad_set_arr_var ('options_done', 'end',   TRUE); 
  pad_data_filter_go ($pad_data[$pad_lvl], $pad_filter_start, $pad_filter_end);    

?>