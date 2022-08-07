<?php

  $pFilter_last = $pPrms_tag ['last'];
  if ( ! $pFilter_last )
    $pFilter_last = 1;

  $pFilter_end   = count($pData[$pad]);
  $pFilter_start = ($pFilter_end - $pFilter_last) + 1;
  
  pDone ( 'last',   TRUE);      
  pData_filter_go ($pData[$pad], $pFilter_start, $pFilter_end);    

?>