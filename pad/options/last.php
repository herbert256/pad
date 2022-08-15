<?php

  $padFilter_last = $padPrmsTag [$pad] ['last'];
  if ( ! $padFilter_last )
    $padFilter_last = 1;

  $padFilter_end   = count($padData [$pad]);
  $padFilter_start = ($padFilter_end - $padFilter_last) + 1;
  
  pDone ( 'last',   TRUE);      
  pData_filter_go ($padData [$pad], $padFilter_start, $padFilter_end);    

?>