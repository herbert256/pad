<?php

#  if ( ! $padSeqName and isset ($padSeqStoreName) and $padSeqStoreName )
 #   $padSeqName = $padSeqStoreName; 

  if ( ! $padSeqName )                     
    $padSeqName = $padSeqSeq; 
  
  $padName [$pad] = $padSeqName;

  $padSeqSetName  = padSeqName ( $padSeqSeq ); 
  $$padSeqSetName = $padSeqParm;

  $padSeqSetName  = padSeqName ( $padSeqName ); 
  $$padSeqSetName = $padSeqParm;

?>