<?php

  $pSeqReturn = [];

  $pSeqNames = array_unique ( ['sequence', $pSeq_seq, $pSeq_set, $pSeq_name, $pName[$p] ] );

  foreach ($pSeq_result as $pSeqValue) {

     $pSeqRecord = [];

     foreach ($pSeqNames as $pSeqName)
       $pSeqRecord [$pSeqName] = $pSeqValue;

     $pSeqReturn [] = $pSeqRecord; 

  } 

?>