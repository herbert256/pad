<?php
    
  $pBtwCnt++;

  $pWords         = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag [$p]      = trim($pWords[0] ?? '');
  $pPrms [$p]     = trim($pWords[1] ?? '');
  $pPrmsType [$p] = ( $pPrms [$p]) ? 'open' : 'none';

  if ( $pTrace ) {
    $pBetweenTrace = ['between' => $pBetween, 'tag' => $pTag[$pP], 'prms' => $pPrms[$pP] ];
    pFile_put_contents ( $pLevelDir [$pP] . "/between/" . $pBtwCnt . ".json", $pBetweenTrace ); 
  }

?>