<?php

  $pad_filter_last = $pad_prms_tag ['last'];
  if ( ! $pad_filter_last )
    $pad_filter_last = 1;

  $pad_filter_end   = count($pad_data[$pad]);
  $pad_filter_start = ($pad_filter_end - $pad_filter_last) + 1;
  
  pad_done (, 'last',   TRUE);      
  pad_data_filter_go ($pad_data[$pad], $pad_filter_start, $pad_filter_end);    

?>