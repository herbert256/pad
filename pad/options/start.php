<?php

  $padFilterStart = $padPrmsTag [$pad] ['start'] ?? 1;
  $padFilterEnd   = $padPrmsTag [$pad] ['end'] ?? count($padData [$pad]);

  padDone ( 'start', TRUE);
  padDone ( 'end',   TRUE); 
  padDataFilterGo ($padData [$pad], $padFilterStart, $padFilterEnd);    

?>