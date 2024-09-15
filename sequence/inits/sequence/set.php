<?php

  if ( $padSeqSetSeq ) {
    $padSeqSeq  = $padSeqSetSeq;
    $padSeqParm = $padSeqSetParm;
  }

  if ( $padSeqSetStore ) {
    $padSeqSeq  = $padSeqSetStore;
    $padSeqParm = $padSeqSetParm;
    include '/pad/sequence/inits/sequence/store.php';
  }

?>