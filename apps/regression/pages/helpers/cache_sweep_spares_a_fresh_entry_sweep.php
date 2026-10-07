<?php

  // The fixture of cache_sweep_spares_a_fresh_entry: the hourly sweep of expired entries,
  // run again and again.

  $sweepDir = padCacheAppDir ();

  for ( $sweepRound = 0; $sweepRound < 1500; $sweepRound++ ) {
    @unlink ( $sweepDir . '.swept' );
    padCacheAppSweep ( $sweepDir );
  }

  $sweepAnswer = 'swept';

?>
