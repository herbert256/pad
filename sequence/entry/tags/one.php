<?php

  foreach ( $padPrm [$pad] as $padSeqOneName => $padSeqOneParm )

    if ( file_exists ( "/pad/sequence/one/types/$padSeqOneName.php" ) )  {

      if ( $padSeqOneName == 'min'    ) $padSeqMin    = PHP_INT_MIN;
      if ( $padSeqOneName == 'max'    ) $padSeqMax    = PHP_INT_MAX;
      if ( $padSeqOneName == 'random' ) $padSeqRandom = ''; 

      $padSeqNoNo [$padSeqOneName] = TRUE;
      unset ( $padSeqEntryList [$padSeqOneName] );

      if ( $padSeqOneParm === TRUE )
        $padSeqOneParm = '';

      $padSeqOneExplode = padExplode ( $padSeqOneParm, '|' ); 
      $padSeqOneStore   = $padSeqOneExplode [0] ?? '';

      if ( isset ( $padSeqStore [$padSeqOneStore] ) ) {
        $padSeqSetStore = $padSeqOneStore;
        unset ( $padSeqOneExplode [0] );
        $padSeqOneParm  = implode ( '|', $padSeqOneExplode );
      } 

      $padSeqOptions [] = [ 
        'padPrmName'  => 'one',
        'padPrmValue' => "$padSeqOneName|$padSeqOneParm"
      ]; 

      return include '/pad/sequence/sequence.php';

    }

  return padError ( 'One not found' );

?>