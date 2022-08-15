<?php

  $padSeqReturn = [];

  $padSeqNames = array_unique ( [
    'sequence', $padSeq_seq, $padSeq_set, $padSeq_name, $padName[$pad], $padPrmsTag[$pad]['toData']??'' 
  ] );

  foreach ($padSeq_result as $padSeqValue) {

     $padSeqRecord = [];

     foreach ($padSeqNames as $padSeqName)
       $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 

?>