<?php

  if ( isset ( $padPrm [$pad] [$padSeqTmp]) )
    $padSeqParm = $padPrm [$pad][$padSeqTmp];
  else
    $padSeqParm = $padOpt [$pad] [1];

  $padSeqParms = padExplode($padSeqParm, '|');

  $padSeqSeq  = 'pull';
  $padSeqPull = $padSeqParms[0]; 
  $padSeqSet  = $padSeqTmp;

  unset ( $padSeqParms[0] );

  if ( count($padSeqParms) )
    $padPrm [$pad] [$padSeqTmp] = implode('|', $padSeqParms);
  else
    $padPrm [$pad] [$padSeqTmp] = true;

?>