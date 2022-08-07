<?php

  $pFilter_first = $pPrmsTag [$p] ['first'];
  if ( ! $pFilter_first )
    $pFilter_first = 1;

	$pFilter_start = 1;
	$pFilter_end   = ($pFilter_start + $pFilter_first) - 1;
     
  pDone ( 'first', TRUE);      
	pData_filter_go ($pData[$p], $pFilter_start, $pFilter_end);    

?>