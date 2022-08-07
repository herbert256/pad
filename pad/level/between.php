<?php
    
  $pT = $p + 1;
  
  $pFirst         = substr($pBetween, 0, 1);
  $pWords         = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag[$pT]      = trim($pWords[0] ?? '');
  $pPrms[$pT]     = trim($pWords[1] ?? '');
  $pPrmsType[$pT] = ( $pPrms[$pT]) ? 'open' : 'none';

?>