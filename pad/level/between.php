<?php
    
  $padBtwCnt++;
  
  $padPrm [$pad] = [];

  $padWords           = preg_split("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);
  $padTag [$pad]      = trim($padWords[0] ?? '');
  $padPrm [$pad] [0]  = trim($padWords[1] ?? '');
  $padPrmType [$pad] = ( $padPrm [$pad] [0]) ? 'open' : 'none';

?>