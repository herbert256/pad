<?php

  $padSeqTmp1 = include '/pad/sequence/actions/types/onlyNow.php';
  $padSeqTmp2 = include '/pad/sequence/actions/types/onlyStore.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>