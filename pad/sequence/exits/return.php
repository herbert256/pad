<?php

  $padSeqReturn = [];

  $padSeqNames = array_unique ( [ 
    $padPrm [$pad] ['name']   ?? '', 
    $padPrm [$pad] ['toData'] ?? '', 
    'sequence', 
    $padSeqSeq, 
    $padSeqSet, 
    $padSeqName, 
    $padName[$pad], 
  ] );

  foreach ($padSeqResult as $padSeqValue  ) {

     $padSeqRecord = [];

     foreach ( $padSeqNames as $padSeqName )
       if ( $padSeqName )
         $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 

  if ( $padTraceActive )
    include pad . 'tail/types/trace/items/sequence.php';

?>