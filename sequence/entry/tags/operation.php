<?php

  foreach ( $padPrm [$pad] as $padSeqEntryName => $padSeqEntryParm )
   
    if ( file_exists ( "/pad/sequence/types/$padSeqEntryName/flags/operationDouble") 
      or file_exists ( "/pad/sequence/types/$padSeqEntryName/flags/operationSingle") )

      return include '/pad/sequence/entry/_lib/operation.php';

  return padError ( 'Action not found' );

?>