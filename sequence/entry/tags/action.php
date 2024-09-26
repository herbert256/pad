<?php

  foreach ( $padPrm [$pad] as $padSeqEntryName => $padSeqEntryParm )

    if ( file_exists ( "/pad/sequence/actions/types/$padSeqEntryName.php" ) ) {
    
      include '/pad/sequence/entry/_lib/entry.php';

      $padSeqOptions [] = [ 
        'padPrmName'  => $padSeqEntryName,
        'padPrmValue' => $padSeqEntryParm
      ];

      return include '/pad/sequence/sequence.php';
      
    }

  return padError ( 'Action not found' );

?>