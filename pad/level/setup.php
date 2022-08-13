<?php
  
  $pP = $p;
  
  $p++;  

  $pTag         [$p] = '';
  $pType        [$p] = '';

  $pPair        [$p] = FALSE;

  $pTrue        [$p] = '';
  $pFalse       [$p] = '';

  $pPrm         [$p] = ''; 
  $pPrms        [$p] = '';
  $pPrmsType    [$p] = '';
  $pPrmsTag     [$p] = [];
  $pPrmsVal     [$p] = [];

  $pName        [$p] = '';

  $pData        [$p] = pDefault_data ();
  $pCurrent     [$p] = [];
  $pKey         [$p] = 1;
  $pDefault     [$p] = TRUE;

  $pWalk        [$p] = 'start';

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

  $pLevelDir    [$p] = $pLevelDir [$p-1] ?? $pTraceDir;
  $pOccurDir    [$p] = $pOccurDir [$p-1] ?? $pTraceDir;

  $pSave_vars   [$p] = [];
  $pDelete_vars [$p] = [];
  $pSet_save    [$p] = [];
  $pSet_delete  [$p] = [];

  $pTagCnt      [$p] = 0;

?>