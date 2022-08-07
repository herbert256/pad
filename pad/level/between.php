<?php
    
  $pFirst        = substr($pBetween, 0, 1);
  $pWords        = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag [$p]      = trim($pWords[0] ?? '');
  $pPrms [$p]     = trim($pWords[1] ?? '');
  $pPrmsType [$p] = ( $pPrms [$p]) ? 'open' : 'none';

?>