<?php


  return;

  
  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) {

    padInfoXapp ( 'sequences', 'types',  $padSeqSeq   );
    padInfoXapp ( 'sequences', 'builds', $padSeqBuild );

    foreach ( $padSeqActions as $padSeqAction ) {
      extract ( $padSeqAction );
      padInfoXapp ( 'sequences', 'actions', $padSeqActionName );
    }

    foreach ( $padSeqStoreList as $padSeqStoreEntry ) {
      extract ( $padSeqStoreEntry );
      padInfoXapp ( 'sequences', 'stores', $padSeqStoreAction );
    }

    foreach ( $padSeqOperations as $padSeqOperation ) {
      extract ( $padSeqOperation );
      padInfoXapp ( 'sequences', 'operations', $padSeqSeq );
      padInfoXapp ( 'sequences', 'builds',     $padSeqBuild );
    }

    foreach ( $padSeqOptions as $padK => $padV ) {
      extract ( $padV );
      padInfoXapp ( 'sequences', 'options', $padK );
    }

  }


  if ( $GLOBALS ['padInfoXref'] ) 
    padInfoXref ( 'sequences', 'builds', $padSeqBuild );


  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceSequence'] ) {

    padInfoTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 
 
  }


?>