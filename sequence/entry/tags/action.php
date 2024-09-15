<?php

  foreach ( $padPrm [$pad] as $padSeqEntryName => $padSeqEntryParm )
    if ( file_exists ( "/pad/sequence/actions/types/$padSeqEntryName.php" ) ) 
      return include '/pad/sequence/entry/_lib/action.php';

  return padError ( 'Action not found' );

?>