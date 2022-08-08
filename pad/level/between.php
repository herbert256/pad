<?php
    
  $pBtwCnt++;

  $pFirst        = substr($pBetween, 0, 1);
  $pWords        = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag [$p]      = trim($pWords[0] ?? '');
  $pPrms [$p]     = trim($pWords[1] ?? '');
  $pPrmsType [$p] = ( $pPrms [$p]) ? 'open' : 'none';

  if ( $p > 1 and $pTrace ) {
    $pBetweenTrace = ['between' => $pBetween, 'tag' => $pTag[$p], 'prms' => $pPrms[$p] ];
    pFile_put_contents ( $pLevelDir [$p] . "/between." . $pBtwCnt . ".json", $pBetweenTrace ); 
  }

?>