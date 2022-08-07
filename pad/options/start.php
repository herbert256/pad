<?php

  $pFilter_start = $pPrmsTag[$p] ['start'] ?? 1;
  $pFilter_end   = $pPrmsTag[$p] ['end'] ?? count($pData[$p]);

  pDone ( 'start', TRUE);
  pDone ( 'end',   TRUE); 
  pData_filter_go ($pData[$p], $pFilter_start, $pFilter_end);    

?>