<?php

  if ( isset ( $padPrm [$pad] [$padSeqChk]) )
    $padSeqParm = $padPrm [$pad][$padSeqChk];
  else
    $padSeqParm = $padSeqChk;

  $padSeqParms = padExplode($padSeqParm, '|');

  $padSeqSeq  = 'pull';
  $padSeqPull = $padSeqParms[0]; 
  $padSeqSet  = $padSeqChk;

  unset ( $padSeqParms[0] );

  if ( count($padSeqParms) )
    $padPrm [$pad] [$padSeqChk] = implode('|', $padSeqParms);
  else
    $padPrm [$pad] [$padSeqChk] = true;

?>