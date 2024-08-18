<?php

  $padSeqTmp1 = include '/pad/sequence/actions/types/onlyMe.php';
  $padSeqTmp2 = include '/pad/sequence/actions/types/onlyOther.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>