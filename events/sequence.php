<?php


  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] ) {
    padInfoXapp ( 'sequences', 'types',  $padSeqSeq );
    padInfoXapp ( 'sequences', 'builds', $padSeqBuild );
  }


  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceSequence'] ) {

    padInfoTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 
    padInfoTrace ( 'sequence', $padSeqSeq, $padSeqReturn ); 
 
    padInfoTrace ( 'sequences', 'types', $padSeqSeq );

    if ( $padSeqFor !== FALSE )
     padInfoTrace ( 'sequences', 'builds', 'for' );
    else
     padInfoTrace ( 'sequences', 'builds', $padSeqBuild );

  }

?>