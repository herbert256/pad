<?php

  $p = 0;

  $pTag         [$p] = '';
  $pType        [$p] = '';

  $pPrm         [$p] = ''; 

  $pTagData     [$p] = '';
  $pTrue        [$p] = '';

  $pFalse       [$p] = '';
  $pPair        [$p] = FALSE;

  $pPrms        [$p] = '';
  $pPrmsType    [$p] = '';
  $pPrmsTag     [$p] = [];
  $pPrmsVal     [$p] = [];

  $pName        [$p] = '';

  $pData        [$p] = [];
  $pCurrent     [$p] = [];
  $pKey         [$p] = 0;
  $pDefault     [$p] = FALSE;

  $pWalk        [$p] = '';
  $pWalkData    [$p] = [];
  
  $pDone        [$p] = [];
  $pOccur       [$p] = 0;
  $pStart       [$p] = 0;
  $pEnd         [$p] = 0;

  $pBase        [$p] = '';
  $pHtml        [$p] = '';
  $pResult      [$p] = '';
 
  $pHit         [$p] = FALSE;
  $pNull        [$p] = FALSE;
  $pElse        [$p] = FALSE;
  $pArray       [$p] = FALSE;
  $pText        [$p] = FALSE;

  $pLevelDir    [$p] = $pLevelDir [$p-1] ?? $pTraceDir ?? '';
  $pOccurDir    [$p] = $pOccurDir [$p-1] ?? $pTraceDir ?? '';

  $pData        [$p] = pDefault_data ();
  $pCurrent     [$p] = [];
  $pKey         [$p] = 1;
  $pDefault     [$p] = TRUE;

  $pWalk        [$p] = 'start';
  
  $pTagCnt      [$p] = 0;
  
  // $pN          = 2
  // include PAD . 'level/setup.php';      

  // $pBetween    = 'start';
  // include PAD . 'level/between.php'; 
  // include PAD . 'level/parms.php'; 

?>