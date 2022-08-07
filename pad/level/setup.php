<?php
  
  include 'between.php';

  $pData        [$pad] = pDefault_data ();
  $pCurrent     [$pad] = reset ( $pData[$pad] );
  $pKey         [$pad] = key ( $pData[$pad] );

  $pad_walks       [$pad] = 'start';
  $pad_walks_data  [$pad] = [];
  
  $pDone        [$pad] = [];
  $pOccur       [$pad] = 0;
  $pStart       [$pad] = 0;
  $pEnd         [$pad] = 0;
  $pDb          [$pad] = '';
  $pDb_lvl      [$pad] = [];

  $pSave_vars   [$pad] = [];
  $pDelete_vars [$pad] = [];
  $pSet_save    [$pad] = [];
  $pSet_delete  [$pad] = [];

  $pTrue        [$pad] = '';
  $pFalse       [$pad] = '';
  $pBase        [$pad] = '';
  $pHtml        [$pad] = '';
  $pResult      [$pad] = '';

  $pParms [$pad] ['prms_type'] = $pPrms_type ?? 'open';
  $pParms [$pad] ['tag_cnt']   = 0;
  $pParms [$pad] ['pair']      = $pPair ?? FALSE;
  $pParms [$pad] ['type']      = $pParms [$pad] ['type'] ?? $pType ?? '';
  $pParms [$pad] ['hit']       = TRUE;
  $pParms [$pad] ['null']      = FALSE;
  $pParms [$pad] ['else']      = FALSE;
  $pParms [$pad] ['array']     = FALSE;
  $pParms [$pad] ['text']      = TRUE;
  $pParms [$pad] ['default']   = pIs_default_data ( $pData [$pad] );
  $pParms [$pad] ['level_dir'] = $pLevel_dir;
  $pParms [$pad] ['occur_dir'] = $pOccur_dir;

?>