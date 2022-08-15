<?php

    $padSeq_seq = $padSeq_tmp;
    $padSeq_set = $padSeq_tmp;

    if ( isset($padPrmsTag [$pad][$padSeq_seq]) )
      $padSeq_parm = $padPrmsTag [$pad][$padSeq_seq];
    else
      $padSeq_parm = $padPrm [$pad];

?>