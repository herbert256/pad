<?php

  $padSeqTmp1 = include '/pad/seq/actions/onlyMe.php';
  $padSeqTmp2 = include '/pad/seq/actions/onlyOther.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>