<?php

  // The fixture of cache_sweep_spares_a_fresh_entry: over and over, an entry is left
  // expired - as one from a run of over an hour ago stands - then put fresh, and asked for
  // at once. Answers how many of the fresh entries were there. Each set= its own keys.

  $sweepSet  = preg_replace ( '/[^a-z0-9]/', '', (string) padRequest ( 'set', 'a' ) );
  $sweepKept = 0;

  for ( $sweepRound = 0; $sweepRound < 400; $sweepRound++ ) {

    $sweepKey  = "sweep-race-$sweepSet-" . ( $sweepRound % 10 );
    $sweepFile = padCacheAppFile ( $sweepKey, 'cache', 'padCachePut' );

    if ( ! is_dir ( dirname ( $sweepFile ) ) )
      mkdir ( dirname ( $sweepFile ), 0755, TRUE );

    file_put_contents ( "$sweepFile.$sweepSet.old", "1\n" . serialize ( 'old' ) );
    rename ( "$sweepFile.$sweepSet.old", $sweepFile );

    padCachePut ( $sweepKey, 'fresh' );

    if ( padCacheHas ( $sweepKey ) )
      $sweepKept++;

  }

  for ( $sweepRound = 0; $sweepRound < 10; $sweepRound++ )
    padCacheForget ( "sweep-race-$sweepSet-$sweepRound" );

  $sweepAnswer = "kept $sweepKept of 400";

?>
