<?php

  $padSeqTmp1 = include PAD . 'seq/actions/types/onlyNow.php';
  $padSeqTmp2 = include PAD . 'seq/actions/types/onlyStore.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>