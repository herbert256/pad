<?php

  $padSeqReturn = [];

  $padSeqNames = array_unique ( [ 
    $padPrm [$pad] ['name']   ?? '', 
    $padPrm [$pad] ['toData'] ?? '', 
    'sequence', 
    $padSeqSeq, 
    $padSeqName, 
    $padSeqPull,
    $padName [$pad], 
    $padTag [$pad], 
  ] );

  foreach ($padSeqResult as $padSeqValue  ) {

     $padSeqRecord = [];

     foreach ( $padSeqNames as $padSeqName )
       if ( $padSeqName )
         $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 


?>