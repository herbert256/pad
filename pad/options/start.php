<?php

  $padFilter_start = $padPrmsTag [$pad] ['start'] ?? 1;
  $padFilter_end   = $padPrmsTag [$pad] ['end'] ?? count($padData [$pad]);

  pDone ( 'start', TRUE);
  pDone ( 'end',   TRUE); 
  pData_filter_go ($padData [$pad], $padFilter_start, $padFilter_end);    

?>