<?php

  $padFilter_start = $padPrmsTag [$pad] ['start'] ?? 1;
  $padFilter_end   = $padPrmsTag [$pad] ['end'] ?? count($padData [$pad]);

  padDone ( 'start', TRUE);
  padDone ( 'end',   TRUE); 
  padDataFilterGo ($padData [$pad], $padFilter_start, $padFilter_end);    

?>