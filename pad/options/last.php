<?php

  $padFilter_last = $padPrmsTag [$pad] ['last'];
  if ( ! $padFilter_last )
    $padFilter_last = 1;

  $padFilter_end   = count($padData [$pad]);
  $padFilter_start = ($padFilter_end - $padFilter_last) + 1;
  
  padDone ( 'last',   TRUE);      
  padDataFilterGo ($padData [$pad], $padFilter_start, $padFilter_end);    

?>