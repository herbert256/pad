<?php

  if ( isset($pPrms_tag[$pSeq_tmp]) )
    $pSeq_parm = $pPrms_tag[$pSeq_tmp];
  else
    $pSeq_parm = $pParm;

  $pSeq_parms = pExplode($pSeq_parm, '|');

  $pSeq_seq  = 'pull';
  $pSeq_pull = $pSeq_parms[0]; 
  $pSeq_set  = $pSeq_tmp;

  unset ( $pSeq_parms[0] );

  if ( count($pSeq_parms) )
    $pPrms_tag [$pSeq_tmp] = implode('|', $pSeq_parms);
  else
    $pPrms_tag [$pSeq_tmp] = true;

  $pParms [$p] ['parms_tag'] [$pSeq_tmp] = $pPrms_tag [$pSeq_tmp];

?>