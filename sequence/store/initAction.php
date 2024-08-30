<?php

  if ( ! $padSeqSeq or $padSeqSeq == 'random' or $padSeqSeq == $padSeqSeqSave or $padSeqSeq == $padSeqPull )
    continue;

  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  include "/pad/sequence/build/include.php";

  $padSeqStoreList [] = [
    'padSeqStoreType'  => 'action',
    'padSeqStoreName'  => $padPrmValue,
    'padSeqActionName' => $padSeqStoreAction
  ];

?>