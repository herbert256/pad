<?php

  foreach ( $padPrm [$pad] as $padSeqEntryName => $padSeqEntryParm )
    if ( file_exists ( "/pad/sequence/one/types/$padSeqEntryName.php" ) ) 
      return include '/pad/sequence/entry/_lib/one.php';

  return padError ( 'One not found' );

?>