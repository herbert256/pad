<?php
  
  include 'between.php';

  $pData       [$p] = pDefault_data ();
  $pCurrent    [$p] = reset ( $pData[$p] );
  $pKey        [$p] = key ( $pData[$p] );

  $pad_walks      [$p] = 'start';
  $pad_walks_data [$p] = [];
  
  $pDone       [$p] = [];
  $pOccur      [$p] = 0;
  $pStart      [$p] = 0;
  $pEnd        [$p] = 0;
  $pDb         [$p] = '';
  $pDb_lvl     [$p] = [];

  $pSave_vars  [$p] = [];
  $pDelete_vars[$p] = [];
  $pSet_save   [$p] = [];
  $pSet_delete [$p] = [];

  $pTrue       [$p] = '';
  $pFalse      [$p] = '';
  $pBase       [$p] = '';
  $pHtml       [$p] = '';
  $pResult     [$p] = '';

  $pPrmsType[$p] = $pPrmsType ?? 'open';
  $TagCnt[$p]   = 0;
  $pPair[$p]      = $pPair ?? FALSE;
  $pType[$p]      = $pType[$p] ?? $pType ?? '';
  $pHit[$p]       = TRUE;
  $pNull[$p]     = FALSE;
  $pElse[$p]     = FALSE;
  $pArray[$p]    = FALSE;
  $pText[$p]     = TRUE;
  $pDefault[$p]  = pIs_default_data ( $pData[$p] );
  $pLevelDir[$p]= $pLevelDir;
  $pOccurDir[$p]= $pOccurDir;

?>    