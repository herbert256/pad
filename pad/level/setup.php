<?php
  
  include 'between.php';

  $pData        [$p] = pDefault_data ();
  $pCurrent     [$p] = reset ( $pData[$p] );
  $pKey         [$p] = key ( $pData[$p] );

  $pad_walks       [$p] = 'start';
  $pad_walks_data  [$p] = [];
  
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

  $pTrue        [$p] = '';
  $pFalse       [$p] = '';
  $pBase        [$p] = '';
  $pHtml        [$p] = '';
  $pResult      [$p] = '';

  $pParms [$p] ['prms_type'] = $pPrms_type ?? 'open';
  $pParms [$p] ['tag_cnt']   = 0;
  $pParms [$p] ['pair']      = $pPair ?? FALSE;
  $pParms [$p] ['type']      = $pParms [$p] ['type'] ?? $pType ?? '';
  $pParms [$p] ['hit']       = TRUE;
  $pParms [$p] ['null']      = FALSE;
  $pParms [$p] ['else']      = FALSE;
  $pParms [$p] ['array']     = FALSE;
  $pParms [$p] ['text']      = TRUE;
  $pParms [$p] ['default']   = pIs_default_data ( $pData [$p] );
  $pParms [$p] ['level_dir'] = $pLevel_dir;
  $pParms [$p] ['occur_dir'] = $pOccur_dir;

?>