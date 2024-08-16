<?php

  $padFilterStart = $padPrm [$pad] ['start'] ?? 1;
  $padFilterEnd   = $padPrm [$pad] ['end'] ?? count($padData [$pad]);

  padDone ( 'start', TRUE); 
  padDone ( 'end',   TRUE); 
  
  padDataFilterGo ($padData [$pad], $padFilterStart, $padFilterEnd);    

?>