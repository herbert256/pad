<?php
  
  $pData        [$p] = pDefault_data ();
  $pCurrent     [$p] = [];
  $pKey         [$p] = 1;
  $pDefault     [$p] = TRUE;

  $pWalk        [$p] = 'start';
  
  $pTagCnt      [$p] = 0;
  $pOccur [$p] = 0;

  $pResult [$p] = '' ;
  $pHtml [$p] = '' ;

  $pSave_vars   [$p] = [];
  $pDelete_vars [$p] = [];
  $pSet_save    [$p] = [];
  $pSet_delete  [$p] = [];

  $pLevelDir    [$p] = $pLevelDir [$p-1] ?? $pTraceDir ?? '';
  $pOccurDir    [$p] = $pOccurDir [$p-1] ?? $pTraceDir ?? '';

?>