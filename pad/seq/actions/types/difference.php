<?php

  $padSeqTmp1 = include 'seq/actions/types/onlyNow.php';
  $padSeqTmp2 = include 'seq/actions/types/onlyStore.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>