<?php

  if ( ! $padSeqName )                     
    $padSeqName = $padSeqSeq; 
  
  $padName [$pad] = $padSeqName;

  $padSeqSetName  = padSeqName ( $padSeqSeq ); 
  $$padSeqSetName = $padSeqParm;

  $padSeqSetName  = padSeqName ( $padSeqName ); 
  $$padSeqSetName = $padSeqParm;

?>