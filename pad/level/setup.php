<?php
  
  $pData       [$p] = pDefault_data ();
  $pCurrent    [$p] = reset ( $pData[$p] );
  $pKey        [$p] = key ( $pData[$p] );
  $pDefault    [$p] = TRUE;

  $pWalk       [$p] = 'start';
  $pWalkData   [$p] = [];
  
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

  $pBase       [$p] = '';
  $pHtml       [$p] = '';
  $pResult     [$p] = '';

  $TagCnt      [$p] = 0;
  $pHit        [$p] = TRUE;
  $pNull       [$p] = FALSE;
  $pElse       [$p] = FALSE;
  $pArray      [$p] = FALSE;
  $pText       [$p] = TRUE;

  include 'parms.php';
  
?>    