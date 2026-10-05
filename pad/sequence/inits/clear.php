<?php

  // Wipes the subsystem's state so a run starts clean, by dropping every $pq* global.
  //
  // Three are spared: $pqStore, the named sequences that must survive between tags, $pqEntry,
  // which says how this run was started, and the $pqSet* values a direct caller passed in.

  // A run started from an expression lives in the evaluator's function scope: its state is
  // local and starts empty. The globals are the tag run it was evaluated inside, still
  // under way - clearing them took the play an eval= was running out from under it.

  if ( $pqEntry == 'direct' )
    return;

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'pq') )
      if ( $padK != 'pqStore' and ! str_starts_with ( $padK, 'pqSet') and $padK != 'pqEntry' )
        unset ( $GLOBALS [$padK] );

?>