<?php

  $padSeqSeq   = 'start';
  $padSeqBuild = 'start';
  
  $padSeqFixed = [];

  $padSeqPull = $padTag [$pad];

  $padSeqStartParms     = padExplode ( $padOpt [$pad] [1], '|' );
  $padSeqStartOperation = array_shift ( $padSeqStartParms );

  if ( count ( $padSeqStartParms ) )
    $padSeqStartParm = reset ( $padSeqStartParms );
  elseif ( isset ( $padOpt [$pad] [2] ) )
    $padSeqStartParm = $padOpt [$pad] [2] ?? '';
  else
    $padSeqStartParm = '';

  $padSeqFixed = [];
  
?>