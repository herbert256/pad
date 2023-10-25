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

  if ( $padTraceTypes ['sequence'] )
    if ( $padTraceTypes ['level'] and $padTraceTypes ['tree'] )
      padFilePutContents ( "$padTraceDir/sequence.json", $padSeqReturn );

?>