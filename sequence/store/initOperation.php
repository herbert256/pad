<?php

  $padSeqSeq   = $padSeqStoreAction;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  if ( in_array ( $padSeqBuild, ['order','fixed'] ) )
    return;

  include '/pad/sequence/build/include.php';

  $padSeqStoreEntry ['padSeqStoreType'] = 'operation';
  $padSeqStoreEntry ['padSeqBuild']     = $padSeqBuild

?>