<?php
    
  $pFirst          = substr($pBetween, 0, 1);
  $pWords          = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag[$p+1]      = trim($pWords[0] ?? '');
  $pPrms[$p+1]     = trim($pWords[1] ?? '');
  $pPrmsType[$p+1] = ( $pPrms[$p]) ? 'open' : 'none';

?>