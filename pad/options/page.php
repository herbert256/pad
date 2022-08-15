<?php

  $padFilterPage  = (int) ($padPrmsTag [$pad] ['page']  ?? 1);
  $padFilterRows  = (int) ($padPrmsTag [$pad] ['rows'] ?? 10);
  $padFilterStart = ( ($padFilterPage-1) * $padFilterRows ) + 1;
  $padFilterEnd   = ($padFilterStart + $padFilterRows) - 1;

  padDone ( 'page', TRUE);
  padDone ( 'rows', TRUE); 
  padDataFilterGo ($padData [$pad], $padFilterStart, $padFilterEnd);    

?>