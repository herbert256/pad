<?php

  if ( isset($pPrmsTag [$p][$pSeq_tmp]) )
    $pSeq_parm = $pPrmsTag [$p][$pSeq_tmp];
  else
    $pSeq_parm = $pPrm [$p];

  $pSeq_parms = pExplode($pSeq_parm, '|');

  $pSeq_seq  = 'pull';
  $pSeq_pull = $pSeq_parms[0]; 
  $pSeq_set  = $pSeq_tmp;

  unset ( $pSeq_parms[0] );

  if ( count($pSeq_parms) )
    $pPrmsTag [$p] [$pSeq_tmp] = implode('|', $pSeq_parms);
  else
    $pPrmsTag [$p] [$pSeq_tmp] = true;

  pParmsPrmsTag'] [$pSeq_tmp] = $pPrmsTag [$p] [$pSeq_tmp];

?>