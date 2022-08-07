<?php

  $pFilter_start = $pPrms_tag ['start'] ?? 1;
  $pFilter_end   = $pPrms_tag ['end'] ?? count($pData[$p]);

  pDone ( 'start', TRUE);
  pDone ( 'end',   TRUE); 
  pData_filter_go ($pData[$p], $pFilter_start, $pFilter_end);    

?>