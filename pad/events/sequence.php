<?php

  global $padInfoTrace, $padInfoTraceSequence, $padInfoXref;

  if ( $padInfoXref  )
    foreach ( $pqInfo as $pqInfoKey => $pqInfoVal )
      foreach (  $pqInfoVal as $pqInfoVal2 )
        padInfoXref ( 'sequence', $pqInfoKey, $pqInfoVal2 );

  if ( $padInfoTrace and $padInfoTraceSequence )
    padInfoTrace ( 'sequence', $pqSeq, $pqResult );

?>