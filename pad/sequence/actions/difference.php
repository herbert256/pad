<?php

  $padSeqTmp1 = include '/pad/sequence/actions/onlyMe.php';
  $padSeqTmp2 = include '/pad/sequence/actions/onlyOther.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>