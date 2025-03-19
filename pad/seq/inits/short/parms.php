<?php

  $padSeqStartParms = padExplode ( $padOpt [$pad] [1], '|' );
  $padSeqStartSeq   = array_shift ( $padSeqStartParms );
  $padSeqResult     = $padSeqStore [$padSeqStartSeq];

    if ( count ( $padSeqStartParms ) )
      $padSeqStartParm = reset ( $padSeqStartParms );
    elseif ( isset ( $padOpt [$pad] [2] ) )
      $padSeqStartParm = $padOpt [$pad] [2] ?? '';
    else
      $padSeqStartParm = '';

?>