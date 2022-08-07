<?php

  $pFilter_page  = (int) ($pPrms_tag ['page']  ?? 1);
  $pFilter_rows  = (int) ($pPrms_tag ['rows'] ?? 10);
  $pFilter_start = ( ($pFilter_page-1) * $pFilter_rows ) + 1;
  $pFilter_end   = ($pFilter_start + $pFilter_rows) - 1;

  pDone ( 'page', TRUE);
  pDone ( 'rows', TRUE); 
  pData_filter_go ($pData[$pad], $pFilter_start, $pFilter_end);    

?>