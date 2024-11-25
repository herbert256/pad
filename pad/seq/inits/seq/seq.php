<?php

  include 'seq/exits/skip.php';

  if ( ! $padSeqName )
    $padSeqName = $padSeqSeq;

  if ( $padSeqParm === TRUE )
    $padSeqParm = '';
  
  $padSeqDone [] = $padSeqSeq;

?>