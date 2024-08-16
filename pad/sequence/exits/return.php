<?php

  $padSeqReturn = [];

  $padSeqNames = array_unique ( [ 
    $padPrm [$pad] ['name']   ?? '', 
    $padPrm [$pad] ['toData'] ?? '', 
    'sequence', 
    $padSeqSeq, 
    $padSeqSet, 
    $padSeqName, 
    $padSeqPull,
    $padName [$pad], 
  ] );

  foreach ($padSeqResult as $padSeqValue  ) {

     $padSeqRecord = [];

     foreach ( $padSeqNames as $padSeqName )
       if ( $padSeqName )
         $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 

  if ( $GLOBALS ['padInfo'] )
    include '/pad/info/events/sequence.php';

?>