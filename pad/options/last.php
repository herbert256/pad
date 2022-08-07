<?php

  $pFilter_last = $pPrmsTag [$p] ['last'];
  if ( ! $pFilter_last )
    $pFilter_last = 1;

  $pFilter_end   = count($pData [$p]);
  $pFilter_start = ($pFilter_end - $pFilter_last) + 1;
  
  pDone ( 'last',   TRUE);      
  pData_filter_go ($pData [$p], $pFilter_start, $pFilter_end);    

?>