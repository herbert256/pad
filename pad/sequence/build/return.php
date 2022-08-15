<?php

  $padSeqReturn = [];

  $padSeqNames = array_unique ( [
    'sequence', $padSeqSeq, $padSeqSet, $padSeqName, $padName[$pad], $padPrmsTag[$pad]['toData']??'' 
  ] );

  foreach ($padSeqResult as $padSeqValue) {

     $padSeqRecord = [];

     foreach ($padSeqNames as $padSeqName)
       $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 

?>