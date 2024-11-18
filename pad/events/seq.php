<?php

  if ( $GLOBALS ['padInfoXapp'] or $GLOBALS ['padInfoXref'] )
    foreach ( $padSeqInfo as $padSeqInfoKey => $padSeqInfoVal )
      foreach (  $padSeqInfoVal as $padSeqInfoVal2 )
        padInfoXapp ( 'seq', $padSeqInfoKey, $padSeqInfoVal2 );
      
  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceSequence'] )
    padInfoTrace ( 'seq', $padSeqSeq, $padSeqResult ); 

?>