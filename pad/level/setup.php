<?php
  
  $p++;
  
  $pTag         [$p] = 'init';
  $pType        [$p] = 

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
  $pDb          [$p] = '';
  $pDb_lvl      [$p] = [];

  $pSave_vars   [$p] = [];
  $pDelete_vars [$p] = [];
  $pSet_save    [$p] = [];
  $pSet_delete  [$p] = [];

  $pBase        [$p] = '';
  $pHtml        [$p] = '';
  $pResult      [$p] = '';

  $pTagCnt      [$p] = 0;
 
  $pTagResult   [$p] = TRUE;
  $pHit         [$p] = TRUE;
  $pNull        [$p] = FALSE;
  $pElse        [$p] = FALSE;
  $pArray       [$p] = FALSE;
  $pText        [$p] = TRUE;

  $pLevelDir    [$p] = $pTraceDir ?? '';
  $pOccurDir    [$p] = $pTraceDir ?? '';

?>