<?php
  
  include 'parms1.php';
  include 'parms2.php';

  $pParms [$pad] ['tag']       = $pTag;
  $pParms [$pad] ['name']      = $pPrms_tag ['name'] ?? $pParms [$pad] ['tag'];
  $pParms [$pad] ['parm']      = $pPrms_val [0] ?? '';
  $pParms [$pad] ['prms']      = $pPrms;
  $pParms [$pad] ['parms_tag'] = $pPrms_tag;  
  $pParms [$pad] ['parms_val'] = $pPrms_val;

?>