<?php

  $padFilter_page  = (int) ($padPrmsTag [$pad] ['page']  ?? 1);
  $padFilter_rows  = (int) ($padPrmsTag [$pad] ['rows'] ?? 10);
  $padFilter_start = ( ($padFilter_page-1) * $padFilter_rows ) + 1;
  $padFilter_end   = ($padFilter_start + $padFilter_rows) - 1;

  padDone ( 'page', TRUE);
  padDone ( 'rows', TRUE); 
  padDataFilterGo ($padData [$pad], $padFilter_start, $padFilter_end);    

?>