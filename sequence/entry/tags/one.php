<?php

  foreach ( $padPrm [$pad] as $padSeqEntryName => $padSeqEntryParm  )

    if ( file_exists ( "/pad/sequence/one/types/$padSeqEntryName.php" ) )  {

      if ( $padSeqEntryName == 'min'    ) $padSeqMin    = PHP_INT_MIN;
      if ( $padSeqEntryName == 'max'    ) $padSeqMax    = PHP_INT_MAX;
      if ( $padSeqEntryName == 'random' ) $padSeqRandom = ''; 

      include '/pad/sequence/entry/_lib/entry.php';

      $padSeqOptions [] = [ 
        'padPrmName'  => 'one',
        'padPrmValue' => "$padSeqEntryName|$padSeqEntryParm"
      ]; 

      return include '/pad/sequence/sequence.php';

    }

  return padError ( 'One not found' );

?>