<?php
  
  include 'parms1.php';
  include 'parms2.php';

  $pad_parms [$pad] ['tag']       = $pad_tag;
  $pad_parms [$pad] ['name']      = $pad_prms_tag ['name'] ?? $pad_parms [$pad] ['tag'];
  $pad_parms [$pad] ['parm']      = $pad_prms_val [0] ?? '';
  $pad_parms [$pad] ['prms']      = $pad_prms;
  $pad_parms [$pad] ['parms_tag'] = $pad_prms_tag;  
  $pad_parms [$pad] ['parms_val'] = $pad_prms_val;

?>