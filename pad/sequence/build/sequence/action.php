<?php

  if ( isset($padPrmsTag [$pad][$padSeqTmp]) )
    $padSeqParm = $padPrmsTag [$pad][$padSeqTmp];
  else
    $padSeqParm = $padPrm [$pad];

  $padSeqParms = padExplode($padSeqParm, '|');

  $padSeqSeq  = 'pull';
  $padSeqPull = $padSeqParms[0]; 
  $padSeqSet  = $padSeqTmp;

  unset ( $padSeqParms[0] );

  if ( count($padSeqParms) )
    $padPrmsTag [$pad] [$padSeqTmp] = implode('|', $padSeqParms);
  else
    $padPrmsTag [$pad] [$padSeqTmp] = true;

?>