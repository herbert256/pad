<?php

  $padSeqReturn = [];

  $padSeqNames = array_unique ( [
    'sequence', $padSeqSeq, $padSeqSet, $padSeqName, $padName[$pad], 
    $padPrm [$pad] ['toData']??'', $padPrm [$pad] ['name']??''  
  ] );

  foreach ($padSeqResult as $padSeqValue) {

     $padSeqRecord = [];

     foreach ($padSeqNames as $padSeqName)
       if ( $padSeqName)
         $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 

?>