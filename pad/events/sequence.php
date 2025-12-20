<?php

  if ( $GLOBALS ['padInfoXref']  )
    foreach ( $pqInfo as $pqInfoKey => $pqInfoVal )
      foreach (  $pqInfoVal as $pqInfoVal2 )
        padInfoXref ( 'sequence', $pqInfoKey, $pqInfoVal2 );

  if ( $GLOBALS ['padInfoTrace'] and $GLOBALS ['padInfoTraceSequence'] )
    padInfoTrace ( 'sequence', $pqSeq, $pqResult );

?>
