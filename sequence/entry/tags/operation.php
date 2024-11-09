<?php

  foreach ( $padPrm [$pad] as $padSeqEntryName => $padSeqEntryParm )

    if ( file_exists ( "/pad/sequence/types/$padSeqEntryName" ) ) {
    
      include '/pad/sequence/entry/_lib/entry.php';

dump();

      $padSeqOperations [] = [
        'padSeqSeq'   => $padSeqSeq,
        'padSeqParm'  => $padPrmValue,
        'padSeqBuild' => $padSeqBuild,
        'padSeqType'  => $padSeqType
      ];

      return include '/pad/sequence/sequence.php';
      
    }

  return padError ( 'Action not found' );

?>