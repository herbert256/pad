<?php

    $pSeq_seq = $pSeq_tmp;
    $pSeq_set = $pSeq_tmp;

    if ( isset($pPrmsTag[$p][$pSeq_seq]) )
      $pSeq_parm = $pPrmsTag[$p][$pSeq_seq];
    else
      $pSeq_parm = $pPrm[$p];

?>