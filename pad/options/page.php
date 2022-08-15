<?php

  $padFilter_page  = (int) ($padPrmsTag [$pad] ['page']  ?? 1);
  $padFilter_rows  = (int) ($padPrmsTag [$pad] ['rows'] ?? 10);
  $padFilter_start = ( ($padFilter_page-1) * $padFilter_rows ) + 1;
  $padFilter_end   = ($padFilter_start + $padFilter_rows) - 1;

  pDone ( 'page', TRUE);
  pDone ( 'rows', TRUE); 
  pData_filter_go ($padData [$pad], $padFilter_start, $padFilter_end);    

?>