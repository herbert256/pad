<?php 

  if ( $padSeqPullName ) {

    if ( ! $padSeqPull or ! isset ( $padSeqStore [$padSeqPull] ) ) {

      $padSeqPull     = $padSeqPullName;
      $padSeqPullName = '';

    } else {

      if ( $padSeqPullName <> $padSeqPull )
        padError ( "Double store names found: '$padSeqPull' & '$padSeqPullNamea' ");

    }

  }

  if ( ! $padSeqPull or ! isset ( $padSeqStore [$padSeqPull] ) )
    padError ( "Store '$padSeqPull' not found" );

?>