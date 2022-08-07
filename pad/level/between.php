<?php
  
  include 'parms1.php';
  include 'parms2.php';

  $pParms [$p] ['tag']       = $pTag;
  $pParms [$p] ['name']      = $pPrms_tag ['name'] ?? $pParms [$p] ['tag'];
  $pParms [$p] ['parm']      = $pPrms_val [0] ?? '';
  $pParms [$p] ['prms']      = $pPrms;
  $pParms [$p] ['parms_tag'] = $pPrms_tag;  
  $pParms [$p] ['parms_val'] = $pPrms_val;

?>