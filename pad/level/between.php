<?php
    
  $pBtwCnt++;

  $pFirst           = substr($pBetween, 0, 1);
  $pWords           = preg_split("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pTag [$pN]      = trim($pWords[0] ?? '');
  $pPrms [$pN]     = trim($pWords[1] ?? '');
  $pPrmsType [$pN] = ( $pPrms [$pN]) ? 'open' : 'none';

  if ( $pTrace ) {
    $pBetweenTrace = ['between' => $pBetween, 'tag' => $pTag[$pN], 'prms' => $pPrms[$pN] ];
    pFile_put_contents ( $pLevelDir [$p] . "/between/" . $pBtwCnt . ".json", $pBetweenTrace ); 
  }

?>