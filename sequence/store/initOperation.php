<?php

  $padSeqSeq   = $padSeqStoreAction;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  if ( in_array ( $padSeqBuild, ['order','fixed'] ) )
    return;

  include "/pad/sequence/build/include.php";

  $padSeqStoreList [] = [
    'padSeqStoreType' => 'action',
    'padSeqStoreName' => $padPrmValue,
    'padSeqSeq'       => $padSeqSeq,
    'padSeqBuild'     => $padSeqBuild
  ];

?>