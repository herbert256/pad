<?php

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceSequence'] ) {

    padInfoTrace ( 'sequence', $padSeqSeq, $padSeqResult ); 
    padInfoTrace ( 'sequence', $padSeqSeq, $padSeqReturn ); 

    padInfoTrace ( 'sequences', 'types', $padSeqSeq );

    if ( $padSeqFor !== FALSE )
     padInfoTrace ( 'sequences', 'builds', 'for' );
    else
     padInfoTrace ( 'sequences', 'builds', $padSeqBuild );

  }

  if ( $GLOBALS ['padInfoXapp'] ) {

    padInfoXapp ( 'sequences', 'types', $padSeqSeq );

    if ( $padSeqFor !== FALSE )
      padInfoXapp ( 'sequences', 'builds', 'for' );
    else
      padInfoXapp ( 'sequences', 'builds', $padSeqBuild );

  }

?>