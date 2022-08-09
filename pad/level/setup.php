<?php
  
  $p++;
  
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
  $pKey         [$p] = 1;
  $pDefault     [$p] = FALSE;

  $pWalk        [$p] = '';
  $pWalkData    [$p] = [];
  
  $pDone        [$p] = [];
  $pOccur       [$p] = 0;
  $pStart       [$p] = 0;
  $pEnd         [$p] = 0;

  $pSave_vars   [$p] = [];
  $pDelete_vars [$p] = [];
  $pSet_save    [$p] = [];
  $pSet_delete  [$p] = [];

  $pBase        [$p] = '';
  $pHtml        [$p] = '';
  $pResult      [$p] = '';

  $pTagCnt      [$p] = 0;
 
  $pTagResult   [$p] = FALSE;
  $pHit         [$p] = FALSE;
  $pNull        [$p] = FALSE;
  $pElse        [$p] = FALSE;
  $pArray       [$p] = FALSE;
  $pText        [$p] = FALSE;

  $pLevelDir    [$p] = $pTraceDir ?? '';
  $pOccurDir    [$p] = $pTraceDir ?? '';

?>