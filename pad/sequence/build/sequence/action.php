<?php

  if ( isset($padPrmsTag [$pad][$padSeq_tmp]) )
    $padSeq_parm = $padPrmsTag [$pad][$padSeq_tmp];
  else
    $padSeq_parm = $padPrm [$pad];

  $padSeq_parms = padExplode($padSeq_parm, '|');

  $padSeq_seq  = 'pull';
  $padSeq_pull = $padSeq_parms[0]; 
  $padSeq_set  = $padSeq_tmp;

  unset ( $padSeq_parms[0] );

  if ( count($padSeq_parms) )
    $padPrmsTag [$pad] [$padSeq_tmp] = implode('|', $padSeq_parms);
  else
    $padPrmsTag [$pad] [$padSeq_tmp] = true;

?>