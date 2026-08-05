<?php

  // Fires from the sequence subsystem's exit handlers - sequence/exits/start.php once a
  // sequence start type has been resolved, and sequence/exits/info.php at the end of a run.
  //
  // Copies everything the run collected in $pqInfo (start types, actions, options, plays)
  // into the xref report, and traces the sequence with its result when $padInfoTraceSequence
  // is on.

  global $padInfoTrace, $padInfoTraceSequence, $padInfoXref;

  if ( $padInfoXref  )
    foreach ( $pqInfo as $pqInfoKey => $pqInfoVal )
      foreach (  $pqInfoVal as $pqInfoVal2 )
        padInfoXref ( 'sequence', $pqInfoKey, $pqInfoVal2 );

  if ( $padInfoTrace and $padInfoTraceSequence )
    padInfoTrace ( 'sequence', $pqSeq, $pqResult );

?>