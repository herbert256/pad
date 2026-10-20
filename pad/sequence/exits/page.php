<?php

  // Takes the terms of the pages before page= off the run's terms, and with them what the run
  // kept beside each term - its value before the plays, the plays' answers, its place in a
  // pulled store - so the columns of a row stay those of its term. inits/limits.php made the
  // run that much longer.

  if ( ! $pqPageSkip )
    return;

  $pqResult   = array_slice ( $pqResult,   $pqPageSkip );
  $pqOrgHit   = array_slice ( $pqOrgHit,   $pqPageSkip );
  $pqPlaysHit = array_slice ( $pqPlaysHit, $pqPageSkip );
  $pqResultAt = array_slice ( $pqResultAt, $pqPageSkip );

?>
