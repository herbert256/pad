<?php

  $padFilterLast = $padPrm [$pad] ['last'];
  if ( ! $padFilterLast )
    $padFilterLast = 1;

  $padFilterEnd   = count($padData [$pad]);
  $padFilterStart = ($padFilterEnd - $padFilterLast) + 1;
  
  padDone ( 'last',   TRUE);      
  padDataFilterGo ($padData [$pad], $padFilterStart, $padFilterEnd);    

?>