<?php

    $padSeqSeq = $padSeqTmp;
    $padSeqSet = $padSeqTmp;

    if ( isset($padPrmsTag [$pad][$padSeqSeq]) )
      $padSeqParm = $padPrmsTag [$pad][$padSeqSeq];
    else
      $padSeqParm = $padPrm [$pad];

?>