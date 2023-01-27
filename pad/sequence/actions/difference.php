<?php

  $padSeqTmp1 = include 'onlyMe.php';
  $padSeqTmp2 = include 'onlyOther.php';

  foreach ( $padSeqTmp2 as $padSeqAppendKey )
    $padSeqTmp1 [] = $padSeqAppendKey;

  return $padSeqTmp1;

?>